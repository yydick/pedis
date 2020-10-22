<?php

/**
 * 打印logo
 * 
 * Class ShowLogo 打印logo
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-21
 */

namespace Spool\Pedis;

/**
 * 打印logo
 * 
 * Class ShowLogo 打印logo
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-21
 */
class ShowLogo
{
    /**
     * 打印logo
     * 
     * @return string
     */
    public static function show(): string
    {
        $logo = "\033[38;5;3m" . "\n                               .__   
    ____________   ____   ____ |  |  
   /  ___/\____ \ /  _ \ /  _ \|  |  
   \___ \ |  |_> >  <_> |  <_> )  |__
  /____  >|   __/ \____/ \____/|____/
       \/ |__|                       
                    .___.__          
  ______   ____   __| _/|__| ______  
  \____ \_/ __ \ / __ | |  |/  ___/  
  |  |_> >  ___// /_/ | |  |\___ \   
  |   __/ \___  >____ | |__/____  >  
  |__|        \/     \/         \/   \n\n" . "\033[0m";
        return $logo;
    }
}
