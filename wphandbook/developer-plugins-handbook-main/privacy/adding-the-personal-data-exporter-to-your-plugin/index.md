## Adding the Personal Data Exporter to Your Plugin

In WordPress 4.9.6, new tools were added to make compliance easier with laws like the European Union's General Data Protection Regulation, or GDPR for short. Among the tools added is a Personal Data Export tool which supports exporting all the personal data for a given user in a ZIP file. In addition to the personal data stored in things like WordPress comments, plugins can also hook into the exporter feature to export the personal data they collect, whether it be in something like postmeta or even an entirely new Custom Post Type (CPT).

The "key" for all the exports is the user's email address – this was chosen because it supports exporting personal data for both full-fledged registered users and also unregistered users (e.g. like a logged out commenter).

However, since assembling a personal data export could be an intensive process and will likely contain sensitive data, we don't want to just generate it and email it to the requestor without confirming the request, so the admin-facing user interface starts all requests by having the admin enter the username or email address making the request and then sends then a link to click to confirm their request.

Once a request has been confirmed, the admin can generate and download or directly email the personal data export ZIP file for the user, or do the export anyways if the need arises. Inside the ZIP file the user receives, they will find a "mini website" with an index HTML page containing their personal data organized in groups (e.g. a group for comments, etc. )

Whether the admin downloads the personal data export ZIP file or sends it directly to the requestor, the way the personal data export is assembled is identical – and relies on hooking "exporter" callbacks to do the dirty work of collecting all the data for the export. When the admin clicks on the download or email link, an AJAX loop begins that iterates over all the exporters registered in the system, one at a time. In addition to exporters built into core, plugins can register their own exporter callbacks.

The exporter callback interface is designed to be as simple as possible. A exporter callback receives the email address we are working with and a page parameter as well. The page parameter (which starts at 1) is used to avoid plugins potentially causing timeouts by attempting to export all the personal data they've collected at once. A well behaved plugin will limit the amount of data it attempts to erase per page (e.g. 100 posts, 200 comments, etc.)

The exporter callback replies with whatever data it has for that email address and page and whether it is done or not. If a exporter callback reports that it is not done, it will be called again (in a separate request) with the page parameter incremented by 1. Exporter callbacks are expected to return an array of items for the export. Each item contains an a group identifier for the group of which the item is a part (e.g. comments, posts, orders, etc.), an optional group label (translated), an item identifier (e.g. comment-133) and then an array of name, value pairs containing the data to be exported for that item.

It is noteworthy that the value could be a media path, in which case a link to the media file will be added to the index HTML page in the export.

When all the exporters have been called to completion, WordPress first assembles an "index" HTML document that serves as the heart of the export report. If a plugin reports additional data for an item that WordPress or another plugin has already added, all the data for that item will be presented together.

Exports are cached on the server for 3 days and then deleted.

A plugin can register one or more exporters, but most plugins will only need one. Let's work on a hypothetical plugin which adds location data for the commenter to comments.

First, let's assume the plugin has used [`add_comment_meta`](https://developer.wordpress.org/reference/functions/add_comment_meta/) to add location data using `meta_key`'s of `latitude` and `longitude`.

The first thing the plugin needs to do is to create an exporter function that accepts an email address and a page, e.g.:

```
/**
 * Export user meta for a user using the supplied email.
 *
 * @param string $email_address   email address to manipulate
 * @param int    $page            pagination
 *
 * @return array
 */
function wporg_export_user_data_by_email( $email_address, $page = 1 ) {
  $number = 500; // Limit us to avoid timing out
  $page   = (int) $page;

  $export_items = array();

  $comments = get_comments(
    array(
      'author_email' => $email_address,
      'number'       => $number,
      'paged'        => $page,
      'order_by'     => 'comment_ID',
      'order'        => 'ASC',
    )
  );

  foreach ( (array) $comments as $comment ) {
    $latitude  = get_comment_meta( $comment->comment_ID, 'latitude', true );
    $longitude = get_comment_meta( $comment->comment_ID, 'longitude', true );

    // Only add location data to the export if it is not empty.
    if ( ! empty( $latitude ) ) {
      // Most item IDs should look like postType-postID. If you don't have a post, comment or other ID to work with,
      // use a unique value to avoid having this item's export combined in the final report with other items
      // of the same id.
      $item_id = "comment-{$comment->comment_ID}";

      // Core group IDs include 'comments', 'posts', etc. But you can add your own group IDs as needed
      $group_id = 'comments';

      // Optional group label. Core provides these for core groups. If you define your own group, the first
      // exporter to include a label will be used as the group label in the final exported report.
      $group_label = __( 'Comments', 'text-domain' );

      // Plugins can add as many items in the item data array as they want.
      $data = array(
        array(
          'name'  => __( 'Commenter Latitude', 'text-domain' ),
          'value' => $latitude,
        ),
        array(
          'name'  => __( 'Commenter Longitude', 'text-domain' ),
          'value' => $longitude,
        ),
      );

      $export_items[] = array(
        'group_id'    => $group_id,
        'group_label' => $group_label,
        'item_id'     => $item_id,
        'data'        => $data,
      );
    }
  }

  // Tell core if we have more comments to work on still.
  $done = count( $comments ) > $number;
  return array(
    'data' => $export_items,
    'done' => $done,
  );
}
```

The next thing the plugin needs to do is to register the callback by filtering the exporter array using the [`wp_privacy_personal_data_exporters`](https://developer.wordpress.org/reference/hooks/wp_privacy_personal_data_exporters/) filter.

When registering you provide a friendly name for the export (to aid in debugging – this friendly name is not shown to anyone at this time) and the callback, e.g.

```
/**
 * Registers all data exporters.
 *
 * @param array $exporters
 *
 * @return mixed
 */
function wporg_register_user_data_exporters( $exporters ) {
  $exporters['my-plugin-slug'] = array(
    'exporter_friendly_name' => __( 'Comment Location Plugin', 'text-domain' ),
    'callback'               => 'my_plugin_exporter',
  );
  return $exporters;
}

add_filter( 'wp_privacy_personal_data_exporters', 'wporg_register_user_data_exporters' );
```

And that's all there is to it! Your plugin will now provide data for the export!