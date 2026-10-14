<?php

/**
 * ------- Usage ----------------------
 * In AppModel
 *
 *  class AppModel extends Model {
 *      var $actsAs = array('Cakeplus.ValidationErrorI18n');
 *
 *      function beforeValidate(){
 *          $this->replaceValidationErrorMessagesI18n();
 *          return true;
 *      }
 *  }
 *
 *
 * If you want to concatenate field name with each error messages, set true on "withFieldName" option.
 *
 *   var $actsAs = array('Cakeplus.ValidationErrorI18n' => array( 'withFieldName' => true ) );
 *
 *
 * If you want to set error messages in each model
 *
 *  class Post extends AppModel {
 *      function beforeValidate(){
 *          $error_messages = array(
 *              'invalid_email' => __('Invalid Email !!!.',true),
 *          );
 *
 *          $this->setErrorMessageI18n($error_messages, false);
 *          parent::beforeValidate();
 *          return true;
 *      }
 *  }
 *
 *
 * If you want to change all default error messages( not merge ),
 * change this line in a model.  Set "true" on 2nd parameter of setErrorMessageI18n().
 *
 *  $this->setErrorMessageI18n($error_messages, true);
 */
class ValidationErrorI18nBehavior extends ModelBehavior
{
    /**
     * Concatenate a field name with each validation error message in replaceValidationErrorMessagesI18n().
     * Field name is set with gettext __()
     *   true: Concatenate
     *   false: not Concatenate
     *
     * @var bool
     * @access protected
     */
    public $_withFieldName = false;

    /**
     * Error messages
     *
     * @var array
     */
    protected array $_error_messages = [];

    /**
     * Setup
     *
     * @param Model $model
     * @param array $config Param: withFieldName (boolean)
     * @return void
     */
    public function setup(Model $model, $config = []): void
    {
        if (!empty($config['withFieldName'])) {
            $this->_withFieldName = true;
        }
    }

    /**
     * Define default validation error messages
     * $default_error_messages can include gettext __() value.
     *
     * @return array
     */
    protected function _getDefaultErrorMessagesI18n(): array
    {
        //Write Default Error Message
        $default_error_messages = [
            'require' => 'Please be sure to input.',
            'email_invalid' => __('Invalid Email address.'),
            'between' => __('Between %2$d and %3$d characters.'),
        ];

        return $default_error_messages;
    }

    /**
     * Set validation error messages.
     *
     * To change default validation error messages,
     *  set $add_error_message in each model.
     *
     * @param Model $model
     * @param array|null $add_error_message
     * @param bool $all_change_flag
     *    true: change all default validation error messages
     *    false: merge $add_error_message with default validation error messages
     * @return void
     */
    public function setErrorMessageI18n(Model $model, ?array $add_error_message = null, bool $all_change_flag = false): void
    {
        $default_error_messages = $this->_getDefaultErrorMessagesI18n();

        if (!empty($add_error_message)) {
            if ($all_change_flag) {
                $default_error_messages = $add_error_message;
            } else {
                $default_error_messages = array_merge($default_error_messages, $add_error_message);
            }

            $this->_error_messages = $default_error_messages;
        } elseif (empty($this->_error_messages)) {
            $this->_error_messages = $default_error_messages;
        }
    }

    /**
     * get validation error messages
     *
     * @return array
     */
    protected function _getErrorMessageI18n(): array
    {
        return $this->_error_messages;
    }

    /**
     * Replace validation error messages for i18n
     *
     * @param Model $model
     * @return void
     */
    public function replaceValidationErrorMessagesI18n(Model $model): void
    {
        $this->setErrorMessageI18n($model, null, false);

        foreach ($model->validate as $fieldname => $ruleSet) {
            foreach ($ruleSet as $rule => $rule_info) {
                $rule_option = [];
                if (!empty($model->validate[$fieldname][$rule]['rule'])) {
                    $rule_option = $model->validate[$fieldname][$rule]['rule'];
                }

                $error_message_list = $this->_getErrorMessageI18n();

                $error_message = (array_key_exists($rule, $error_message_list) ? $error_message_list[$rule] : null);

                if (!empty($error_message)) {
                    $model->validate[$fieldname][$rule]['message'] = vsprintf($error_message, $rule_option);
                } elseif (!empty($model->validate[$fieldname][$rule]['message'])) {
                    $model->validate[$fieldname][$rule]['message'] = __($model->validate[$fieldname][$rule]['message']);
                }

                if ($this->_withFieldName && !empty($model->validate[$fieldname][$rule]['message'])) {
                    $model->validate[$fieldname][$rule]['message'] = __($fieldname) . ' : ' . $model->validate[$fieldname][$rule]['message'];
                }
            }
        }
    }
}
