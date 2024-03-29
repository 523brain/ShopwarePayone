<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_SessionStatus_Config extends Payone_Config_Abstract
{
    /**
     * @return array
     */
    public function getDefaultConfigData()
    {
        $defaultConfig = array(
            'default' => array(
                'validators' => array(
                    'Payone_SessionStatus_Validator_Ip',
                    'Payone_SessionStatus_Validator_DefaultParameters',
                ),
                'protocol' => array(
                    'filter' => array(
                        'mask_value' => array(
                            'enabled' => 1,
                            'percent' => 100
                        )
                    ),
//                    'loggers' => array(
//                        'Payone_Protocol_Logger_Log4php' => array(
//                            'filename' => 'payone_sessionstatus.log',
//                            'max_file_size' => '1MB',
//                            'max_file_count' => 20
//                        )
//                    ),
                ),
            )
        );
        return $defaultConfig;
    }

}
