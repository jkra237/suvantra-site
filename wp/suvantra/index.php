<?php
/**
 * Fallback template. WordPress requires it; this site has no posts, so the
 * only things that land here are the blog leftovers — a post, a category
 * archive, an author archive — and addresses that do not exist.
 *
 * The status code matters. Without status_header() these pages answer with
 * HTTP 200 while showing "Page not found": a soft 404, which search engines
 * treat as a defect and which keeps the address in the index. The visible page
 * and the status code have to say the same thing.
 *
 * If a blog is ever added, this file becomes a real archive template and the
 * status line has to go.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

status_header( 404 );
nocache_headers();
get_template_part( '404' );
