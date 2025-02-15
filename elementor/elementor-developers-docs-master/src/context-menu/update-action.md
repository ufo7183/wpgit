# Update Action

<Badge type="tip" vertical="top" text="Elementor Core" /> <Badge type="warning" vertical="top" text="Basic" />

To modify an existing action, we need to change the action object value in a specific entry.

## Update Widget Action

In the example below, we'll update the `widget-action` action icon:

```js
elementor.hooks.addFilter( 'elements/context-menu/groups', ( customGroups, elementType ) => {

	if ( 'widget' === elementType ) {
		customGroups.forEach( ( group ) => {
			if ( 'custom-widget-actions' === group.name ) {
				group.actions.forEach( ( action ) => {
					if ( 'widget-action' === action.name ) {
						action.icon = 'eicon-code';
					}
				} );
			}
		} );
	}

	return customGroups;

} );
```

## Update Column Action

Now we'll update the label of a `column-action` action title:

```js
elementor.hooks.addFilter( 'elements/context-menu/groups', ( customGroups, elementType ) => {

	if ( 'column' === elementType ) {
		customGroups.forEach( ( group ) => {
			if ( 'custom-column-actions' === group.name ) {
				group.actions.forEach( ( action ) => {
					if ( 'column-action' === action.name ) {
						action.title = 'New Label';
					}
				} );
			}
		} );
	}

	return customGroups;

} );
```

## Update Section Action

Next we'll update the entire `section-action` action:

```js
elementor.hooks.addFilter( 'elements/context-menu/groups', ( customGroups, elementType ) => {

	if ( 'column' === elementType ) {
		customGroups.forEach( ( group ) => {
			if ( 'custom-column-actions' === group.name ) {
				group.actions.forEach( ( action ) => {
					if ( 'column-action' === action.name ) {
						action.icon = 'eicon-alert';
						action.title = 'Hellooo';
						action.callback = () => alert( 'bla bla' );
					}
				} );
			}
		} );
	}

	return customGroups;

} );
```
