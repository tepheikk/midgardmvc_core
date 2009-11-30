<?php
/**
 * @package midgardmvc_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 *
 * MidgardRootFile for running MidCOM 3 under FastCGI setups like lighttpd
 */
 
// Load MidCOM 3
// Note: your MidCOM base directory has to be in PHP include_path
require('midgardmvc_core/framework.php');
$midgardmvc = midgardmvc_core::get_instance('midgard2');
    
// Process the request
midgardmvc_core::get_instance()->process();

// Serve the request
midgardmvc_core::get_instance()->serve();

// End
unset($midgardmvc);
?>