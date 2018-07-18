<?php
/**
 * Sendinblue Message Class.
 *
 * @category   Yii
 * @package    Sendinblue
 * @subpackage Transactional
 * @author     Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license    BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @link       http://www.silverbackstudio.it/
 */

namespace yii\sendinblue\transactional;

use Yii;
use yii\mail\BaseMessage;

/**
 * Message is the class that is used to store the data of the email message that
 * will be sent through Sendinblue API.
 *
 * @category Yii
 * @package  Sendinblue
 * @author   Brando Meniconi <b.meniconi@silverbackstudio.it>
 * @license  BSD-3-Clause https://opensource.org/licenses/BSD-3-Clause 
 * @version  Release: 1.0
 * @link     http://www.silverbackstudio.it/
 */
class Message extends BaseMessage
{

    /**
     * The name of the template inside Sendinblue.
     *
     * @var   string
     * @since 1.0.0
     */
    public $template = null;
    
    /**
     * The name of the template inside Sendinblue.
     *
     * @var   string
     * @since 1.0.0
     */
    public $attributes;    

    /**
     * Return the active template
     *
     * @since 1.0.0
     *
     * @return int
     */
    public function getTemplate()
    {
        return $this->template;
    }
    
    /**
     * Sets the active template
     *
     * @param int|string $template The template ID
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function setTemplate( $template )
    {
        $this->template = intval($template);
    }
    
    /**
     * Return the message attributes
     *
     * @since 1.0.0
     *
     * @return array|null
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Sets the message attributes
     *
     * @param array $attributes The atrtibutes to set in the message
     *
     * @since  1.0.0 
     * @return void
     */
    public function setAttributes( $attributes )
    {
        $this->attributes = $attributes;
    }    

}
