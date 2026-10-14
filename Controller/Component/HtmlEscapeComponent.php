<?php

App::uses('Component', 'Controller');

/**
 * Plugin component : Execute Html Escape and nl2br to Array Data
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2009, Yasushi Ichikawa. (http://d.hatena.ne.jp/cakephper/)
 * @link          http://d.hatena.ne.jp/cakephper/
 * @package       cakeplus
 * @subpackage    html_escape
 * @version       0.01
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * =====Usage=====
 * // do html escape to pagination data exclude Post.title.
 * //in controller
 * var $components = array( 'Cakeplus.HtmlEscape' );
 *
 * $this->set('posts', $this->HtmlEscape->nl2br_h($this->paginate( 'Post' ),null, array( 'Post.title') ) );
 */
class HtmlEscapeComponent extends Component
{
    /**
     * Execute nl2br() and  h() to Array Data
     *
     * @param array|string $value
     * @param string|null $charset
     * @param array|null $noescape_list
     * @param string|null $parent_key
     * @return array|string
     */
    public function nl2br_h(array|string $value, ?string $charset = null, ?array $noescape_list = null, ?string $parent_key = null)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $parent_key_arr = isset($parent_key) ? $parent_key . '.' . $key : $key;

                $value[$key] = self::nl2br_h($val, $charset, $noescape_list, $parent_key_arr);
            }

            return $value;
        } else {
            if (is_array($noescape_list)) {
                foreach ($noescape_list as $noescape_value) {
                    if (preg_match('/^(.+\.|)' . preg_quote($noescape_value, '/') . '(\..+|)$/', $parent_key)) {
                        return $value;
                    }
                }
            }
            $value = self::_nl2br_h($value, $charset);

            return $value;
        }
    }

    /**
     * Execute nl2br() and  h() to String Data
     *
     * @param string $value
     * @param string|null $charset
     * @return string
     */
    protected static function _nl2br_h($value, ?string $charset = null): string
    {
        return nl2br(h($value, $charset ?? true));
    }
}
