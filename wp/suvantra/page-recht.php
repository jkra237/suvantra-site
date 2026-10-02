<?php
/**
 * Compatibility shim for the renamed template.
 *
 * Until version 1.1 this template was called page-recht.php, and that file
 * name is what WordPress stored on the privacy and imprint pages. Deleting the
 * file would drop those pages back to page.php and lose the narrow column.
 *
 * Deliberately WITHOUT a "Template Name" header: that way it does not appear a
 * second time in the template dropdown, but a page still pointing at it keeps
 * working.
 *
 * Removable once both legal pages have been switched to "Legal text" in the
 * editor sidebar.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require get_template_directory() . '/page-legal.php';
