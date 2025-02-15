'use strict';

const splitPath = require( '../../../../lib/util/split-path' );

describe( 'splitPath utility', () => {

	it( 'is a function', () => {
		expect( typeof splitPath ).toBe( 'function' );
	} );

	it( 'splits a simple path on the "/" character', () => {
		expect( splitPath( 'a/b/c/d' ) ).toEqual( [ 'a', 'b', 'c', 'd' ] );
	} );

	it( 'correctly splits a string containing named capture groups', () => {
		const result = splitPath( '/posts/(?P<parent>[\\d]+)/revisions/(?P<id>[\\d]+)' );
		expect( result ).toEqual( [
			'posts',
			'(?P<parent>[\\d]+)',
			'revisions',
			'(?P<id>[\\d]+)',
		] );
	} );

	it( 'correctly splits a string when a named group regex contains a "/"', () => {
		const result = splitPath( '/plugin/(?P<plugin>[a-z\\/\\.\\-_]+)' );
		expect( result ).toEqual( [
			'plugin',
			'(?P<plugin>[a-z\\/\\.\\-_]+)',
		] );
	} );

	it( 'correctly splits a string with levels containing text outside named groups', () => {
		// From user-contributed example on https://developer.wordpress.org/reference/functions/register_rest_route/
		// Note that this library does not support this syntax, but ensuring that
		// common variants of path strings are split correctly avoids situations
		// where an unexpected string format could cause an error.
		const result = splitPath( '/users/market=(?P<market>[a-zA-Z0-9-]+)/lat=(?P<lat>[a-z0-9 .\\-]+)/long=(?P<long>[a-z0-9 .\\-]+)' );
		expect( result ).toEqual( [
			'users',
			'market=(?P<market>[a-zA-Z0-9-]+)',
			'lat=(?P<lat>[a-z0-9 .\\-]+)',
			'long=(?P<long>[a-z0-9 .\\-]+)',
		] );
	} );

	it( 'correctly splits a string with this situation', () => {
		const result = splitPath( '/plugin/(?P<plugin_slug>[^/]+)/committers/?' );
		expect( result ).toEqual( [
			'plugin',
			'(?P<plugin_slug>[^/]+)',
			'committers',
			'?',
		] );
	} );

} );
