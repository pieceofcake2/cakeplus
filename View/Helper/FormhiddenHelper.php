<?php

/**
 * Formhidden Helper: create html hidden tags.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2009, Yasushi Ichikawa. (http://d.hatena.ne.jp/cakephper/)
 * @link          http://d.hatena.ne.jp/cakephper/
 * @package       cakeplus
 * @subpackage    cakeplus
 * @version       0.02
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 *
 * =====Usage=====
 * // in controller
 * public $helpers = ['Cakeplus.Formhidden'];
 *
 * // in view(ctp file) for using $this->data
 * <?php echo $formhidden->hiddenVars(); ?>
 *
 * // in view(ctp file) for using  $data_arr parameter
 * <?php echo $formhidden->hiddenVars($data_arr); ?>
 * ===============
 * @property FormHelper $Form
 */
class FormhiddenHelper extends Helper
{
    public $helpers = ['Form'];

    // String data of Hidden tags.
    public string $hidden_output = '';

    /**
     * construct html hidden tag
     *
     * @param array $data_arr // if not set, using $this->data
     * @return string|null
     */
    public function hiddenVars($data_arr = null)
    {
        $data = $this->request->data;

        if (empty($data) && empty($data_arr)) {
            return null;
        }
        if (!is_array($data_arr) || empty($data_arr)) {
            $data_arr = $data;
        }

        $this->_createHidden($data_arr);

        $output = $this->hidden_output;
        $this->hidden_output = '';

        return $output;
    }

    /**
     * @param array|string $data
     * @param string|null $parent_key
     * @return void
     */
    private function _createHidden(array|string $data, ?string $parent_key = null): void
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $parent_key_arr = isset($parent_key) ? $parent_key . '.' . $key : $key;
                $this->_createHidden($val, $parent_key_arr);
            }
        } else {
            $this->hidden_output .= $this->Form->hidden($parent_key) . "\n";
        }
    }
}
