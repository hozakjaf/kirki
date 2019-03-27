<?php
/**
 * Get the URL of any file in WordPress.
 *
 * @package   kirki-framework/url-getter
 * @author    Ari Stathopoulos (@aristath)
 * @copyright Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license   https://opensource.org/licenses/MIT
 * @since     1.0
 */

namespace Kirki;

/**
 * A collection of methods to get the URL of files.
 *
 * @since 1.0
 */
class URL {

	/**
	 * The file path.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $path;

	/**
	 * The content path.
	 *
	 * @static
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private static $content_path;

	/**
	 * The content RL.
	 *
	 * @static
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private static $content_url;

	/**
	 * The file URL.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $url;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $path Absolute path to a file.
	 */
	public function __construct( $path ) {
		$this->path = \wp_normalize_path( $path );
		$this->set_content_url();
		$this->set_content_path();
	}

	/**
	 * Get the file URL.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	function get_url() {

		/**
		 * Start by replacing ABSPATH with site_url.
		 * This is not accurate at all and only serves as a fallback in case everything else fails.
		 */
		$this->url = \str_replace( ABSPATH, \trailingslashit( \site_url() ), $this->path ); 

		/**
		 * If the file-path is inside wp-content replace the content-path with the content-url.
		 * This serves as a fallback in case the other tests below fail.
		 */
		if ( false !== \strpos( $this->path, self::$content_path ) ) {
			$this->url = \str_replace( self::$content_path, self::$content_url, $this->path );
		}

		/**
		 * If the file is in a parent theme use the template directory.
		 */
		if ( $this->in_parent_theme() ) {
			$this->url = \get_template_directory_uri() . \str_replace( \get_template_directory(), '', $this->path );
		}

		/**
		 * If the file is in a child-theme use the stylesheet directory.
		 */
		if ( ! $this->in_parent_theme() && $this->in_child_theme() ) {
			$this->url = \get_stylesheet_directory_uri() . \str_replace( \get_stylesheet_directory(), '', $this->path );
		}

		$this->url = \set_url_scheme( $this->url );
		return \apply_filters( 'kirki_path_url', $this->url, $this->path );
	}

	/**
	 * Check if the path is inside a parent theme.
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function in_parent_theme() {
		return ( 0 === \strpos( $this->path, \get_template_directory() ) );
	}

	/**
	 * Check if the path is inside a child theme.
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function in_child_theme() {
		return ( 0 === \strpos( $this->path, \get_stylesheet_directory() ) );
	}

	/**
	 * Set the $content_url.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function set_content_url() {
		if ( ! self::$content_url ) {
			self::$content_url = \untrailingslashit( \content_url() );
		}
	}

	/**
	 * Set the $content_path.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function set_content_path() {
		if ( ! self::$content_path ) {
			self::$content_path = \wp_normalize_path( \untrailingslashit( WP_CONTENT_DIR ) );
		}
	}
}
