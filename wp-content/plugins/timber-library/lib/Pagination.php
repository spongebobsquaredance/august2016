<?php

namespace Timber;

class Pagination {

	/**
	 * Get pagination.
	 * @api
	 * @param array   $prefs
	 * @return array mixed
	 */
	public static function get_pagination( $prefs ) {
		global $wp_query;
		global $paged;
		global $wp_rewrite;
		$args = array();
		$args['total'] = ceil($wp_query->found_posts / $wp_query->query_vars['posts_per_page']);
		if ( $wp_rewrite->using_permalinks() ) {
			$url = explode('?', get_pagenum_link(0));
			if ( isset($url[1]) ) {
				parse_str($url[1], $query);
				$args['add_args'] = $query;
			}
			$args['format'] = $wp_rewrite->pagination_base.'/%#%';
			$args['base'] = trailingslashit($url[0]).'%_%';
		} else {
			$big = 999999999;
			$args['base'] = str_replace($big, '%#%', esc_url(get_pagenum_link($big)));
		}
		$args['type'] = 'array';
		$args['current'] = max(1, get_query_var('paged'));
		$args['mid_size'] = max(9 - $args['current'], 3);
		if ( is_int($prefs) ) {
			$args['mid_size'] = $prefs - 2;
		} else {
			$args = array_merge($args, $prefs);
		}
		$data = array();
		$data['current'] = $args['current'];
		$data['total'] = $args['total'];
		$data['pages'] = Pagination::paginate_links($args);

		if ( $data['total'] <= count($data['pages']) ) {
			// decrement current so that it matches up with the 0 based index used by the pages array
			$current = $data['current'] - 1;
		} else {
			// $data['current'] can't be used b/c there are more than 10 pages and we are condensing with dots
			foreach ( $data['pages'] as $key => $page ) {
				if ( !empty($page['current']) ) {
					$current = $key;
					break;
				}
			}
		}

		// set next and prev using pages array generated by paginate links
		if ( isset($current) && isset($data['pages'][$current + 1]) ) {
			$data['next'] = array('link' => user_trailingslashit($data['pages'][$current + 1]['link']), 'class' => 'page-numbers next');
			if ( Pagination::is_search_query($data['next']['link']) ) {
				$data['next']['link'] = untrailingslashit($data['next']['link']);
			}
		} 
		if ( isset($current) && isset($data['pages'][$current - 1]) ) {
			$data['prev'] = array('link' => user_trailingslashit($data['pages'][$current - 1]['link']), 'class' => 'page-numbers prev');
			if ( Pagination::is_search_query($data['prev']['link']) ) {
				$data['prev']['link'] = untrailingslashit($data['prev']['link']);
			}
		}
		if ( $paged < 2 ) {
			$data['prev'] = '';
		}
		if ( $data['total'] === (double) 0 ) {
			$data['next'] = '';
		}
		return $data;
	}

	/**
	 * Checks to see whether the given URL has a search query in it (s=*)
	 * @param string $url
	 * @return boolean
	 */
	public static function is_search_query( $url ) {
		if ( strpos($url, 's=') !== false ) {
			return true;
		}
		return false;
	}

	/**
	 *
	 *
	 * @param array  $args
	 * @return array
	 */
	public static function paginate_links( $args = array() ) {
		$defaults = array(
			'base' => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
			'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
			'total' => 1,
			'current' => 0,
			'show_all' => false,
			'prev_next' => false,
			'prev_text' => __('&laquo; Previous'),
			'next_text' => __('Next &raquo;'),
			'end_size' => 1,
			'mid_size' => 2,
			'type' => 'array',
			'add_args' => false, // array of query args to add
			'add_fragment' => ''
		);
		$args = wp_parse_args($args, $defaults);
		// Who knows what else people pass in $args
		$args['total'] = intval((int) $args['total']);
		if ( $args['total'] < 2 ) {
			return array();
		}
		$args['current'] = (int) $args['current'];
		$args['end_size'] = 0 < (int) $args['end_size'] ? (int) $args['end_size'] : 1; // Out of bounds?  Make it the default.
		$args['mid_size'] = 0 <= (int) $args['mid_size'] ? (int) $args['mid_size'] : 2;
		$args['add_args'] = is_array($args['add_args']) ? $args['add_args'] : false;
		$page_links = array();
		$dots = false;
		for ( $n = 1; $n <= $args['total']; $n++ ) {
			$n_display = number_format_i18n($n);
			if ( $n == $args['current'] ) {
				$page_links[] = array(
					'class' => 'page-number page-numbers current',
					'title' => $n_display,
					'text' => $n_display,
					'name' => $n_display,
					'current' => true
				);
				$dots = true;
			} else {
				if ( $args['show_all'] || ($n <= $args['end_size'] || ($args['current'] && $n >= $args['current'] - $args['mid_size'] && $n <= $args['current'] + $args['mid_size']) || $n > $args['total'] - $args['end_size']) ) {
					$link = str_replace('%_%', 1 == $n ? '' : $args['format'], $args['base']);
					$link = str_replace('%#%', $n, $link);
					$link = trailingslashit($link).ltrim($args['add_fragment'], '/');
					if ( $args['add_args'] ) {
						$link = rtrim(add_query_arg($args['add_args'], $link), '/');
					}
					$link = str_replace(' ', '+', $link);
					$link = untrailingslashit($link);
					$link = esc_url(apply_filters('paginate_links', $link));
					$link = user_trailingslashit($link);
					if ( self::is_search_query($link) ) {
						$link = untrailingslashit($link);
					}
					$page_links[] = array(
						'class' => 'page-number page-numbers',
						'link' => $link,
						'title' => $n_display,
						'name' => $n_display,
						'current' => $args['current'] == $n
					);
					$dots = true;
				} elseif ( $dots && !$args['show_all'] ) {
					$page_links[] = array(
						'class' => 'dots',
						'title' => __('&hellip;')
					);
					$dots = false;
				}
			}
		}

		return $page_links;
	}

}