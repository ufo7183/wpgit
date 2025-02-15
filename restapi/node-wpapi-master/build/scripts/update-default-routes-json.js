/**
 * To avoid requiring that auto-discovery be utilized every time the API client
 * is initialized, this library ships with a built-in route definition from a
 * vanilla WordPress REST API installation. That file may be updated by
 * installing the API plugin on a clean WP development instance, with no other
 * plugins running, and downloading the JSON output from `yourwpsite.com/wp-json/`
 * into the "default-routes.json" file in this directory.
 *
 * That file can also be generated by running this script against the same live
 * WP REST API instance to download that same file, the difference being that,
 * if the `default-routes.json` file is downloaded through this script, it
 * will be run through the `simplifyObject` utility to cut out about 1/3 of the
 * bytes of the response by removing properties that do not effect route generation.
 *
 * This script is NOT intended to be a dependency of any part of wp.js, and is
 * provided purely as a utility for upgrading the built-in copy of the endpoint
 * response JSON file that is used to bootstrap the default route handlers.
 *
 * @example
 *
 *     # Invoke directly, run against default endpoint (details below)
 *     ./update-default-routes-json.js
 *
 *     # Invoke with `node` CLI, and run against a custom endpoint
 *     node ./update-default-routes-json --endpoint=http://my-site.com/wp-json
 *
 *     # Invoke with npm script alias, and run against a custom endpoint
 *     npm run update-default-routes-json -- --endpoint=http://my-site.com/wp-json
 *
 * This script runs against http://wpapi.local/wp-json by default, but it can be
 * run against an arbitrary WordPress REST API endpoint by passing the --endpoint
 * argument on the CLI:
 *
 * @example
 *
 *     # Invoke directly, run against an arbitrary WordPress API root
 *     ./update-default-routes-json.js --endpoint=http://my-site.com/wp-json
 *
 *     # Invoke with `node` CLI, run against an arbitrary WordPress API root
 *     node ./update-default-routes-json --endpoint=http://my-site.com/wp-json
 *
 * Either form will update the `default-routes.json` file in this directory,
 * providing that the endpoint data is downloaded successfully.
 *
 * This script also has some utility for downloading a custom JSON file for your
 * own WP REST API-enabled site, so that you can bootstrap your own routes without
 * incurring an HTTP request. To output to a different directory than the default
 * (which is this directory, `lib/data/`), pass an --output argument on the CLI:
 *
 * @example
 *
 *     # Output to your current working directory
 *     ./path/to/this/dir/update-default-routes-json.js --output=.
 *
 *     # Output to an arbitrary absolute path
 *     ./path/to/this/dir/update-default-routes-json.js --output=/home/mordor/output.json
 *
 * These command-line flags may be combined, and you will usually want to use
 * `--endpoint` alongside `--output` to download your own JSON into your own
 * application's directory. The name of the output file can be customized with
 * the `--file` option.
 */
'use strict';

const agent = require( 'superagent' );
const fs = require( 'fs' );
const path = require( 'path' );
const simplifyObject = require( './simplify-object' );

// Parse the arguments object
const argv = require( 'minimist' )( process.argv.slice( 2 ) );

if ( argv.h || argv.help ) {
	console.log( `
Available options:

--endpoint The fully-qualified URI of an API root endpoint to scrape.
--output   The directory to which to output the scraped JSON.
--file     The filename to which to output the scraped JSON.

Examples:

update-default-routes-json \\
  --endpoint=https://wordpress.org/wp-json \\
  --output=lib/data \\
  --file=default-routes.json\n` );
	process.exit();
}

// The output directory defaults to the lib/data directory. To customize it,
// specify your own directory with --output=your/output/directory (supports
// both relative and absolute paths)
const outputPath = argv.output ?
	// Nested ternary, don't try this at home: this is to support absolute paths
	argv.output[ 0 ] === '/' ? argv.output : path.join( process.cwd(), argv.output ) :
	// Output to lib/data/ by default
	path.resolve( process.cwd(), 'lib', 'data' );

// Specify your own API endpoint with --endpoint=http://your-endpoint.com/wp-json
const endpoint = argv.endpoint || 'http://wpapi.local/wp-json';

// Specify a custom output file name with --file=custom-api-routes-filename.json
const fileName = argv.file || 'default-routes.json';

// This directory will be called to kick off the JSON download: it uses
// superagent internally for HTTP transport that respects HTTP redirects.
const getJSON = ( cbFn ) => {
	agent
		.get( endpoint )
		.set( 'Accept', 'application/json' )
		.end( ( err, res ) => {
			// Inspect the error and then the response to infer various error states
			if ( err ) {
				console.error( '\nSomething went wrong! Could not download endpoint JSON.' );
				if ( err.status ) {
					console.error( 'Error ' + err.status );
				}
				if ( err.response && err.response.error ) {
					console.error( err.response.error );
				}
				return process.exit( 1 );
			}

			if ( res.type !== 'application/json' ) {
				console.error( '\nError: expected response type "application/json", got ' + res.type );
				console.error( 'Could not save ' + fileName );
				return process.exit( 1 );
			}

			cbFn( res );
		} );
};

// The only assumption we want to make about the URL is that it should be a web
// URL of _some_ sort, which generally means it has "http" in it somewhere. We
// can't assume much else due to how customizable the location of API root is
// within your WP install.
if ( ! /http/i.test( endpoint ) ) {
	console.error( '\nError: ' + endpoint );
	console.error( 'This does not appear to be a valid URL. Please double-check the URL format\n' +
		'(should be e.g. "http://your-domain.com/wp-json") and try again.' );
	process.exit( 1 );
}

fs.stat( outputPath, ( err, stats ) => {
	if ( err || ! stats.isDirectory() ) {
		console.error( '\nError: ' + outputPath );
		console.error( 'This is not a valid directory. Please double-check the path and try again.' );
		process.exit( 1 );
	}

	// If we made it this far, our arguments look good! Carry on.
	getJSON( ( response ) => {
		// Extract the JSON
		const endpointJSON = JSON.parse( JSON.stringify( response.body ) );
		// Simplify the JSON structure and pick out the routes dictionary
		const slimJSON = simplifyObject( endpointJSON ).routes;

		// Save the file
		const outputFilePath = path.join( outputPath, fileName );
		fs.writeFile( outputFilePath, JSON.stringify( slimJSON ), ( err ) => {
			if ( err ) {
				console.error( '\nSomething went wrong! Could not save ' + outputFilePath );
				return process.exit( 1 );
			}
			console.log( '\nSuccessfully saved ' + outputFilePath );
		} );
	} );
} );
