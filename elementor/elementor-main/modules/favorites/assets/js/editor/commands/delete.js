import CommandsBase from './base';

export class Delete extends CommandsBase {
	apply( args ) {
		const manager = this.component.manager;

		return manager.typeInstance( args.type )?.delete( args.favorite );
	}
}

export default Delete;
