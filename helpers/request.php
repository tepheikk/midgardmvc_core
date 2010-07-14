<?php
/**
 * @package midgardmvc_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Midgard MVC HTTP request and URL mapping helper
 *
 * @package midgardmvc_core
 */
class midgardmvc_core_helpers_request
{
    /**
     * HTTP verb used with request
     */
    private $method = 'get';

    /**
     * HTTP query parameters used with request
     */
    private $query = array();

    /**
     * The root page to be used with the request
     *
     * @var midgardmvc_core_providers_hierarchy_node
     */
    private $root_node = null;

    /**
     * The page to be used with the request
     *
     * @var midgardmvc_core_providers_hierarchy_node
     */
    private $node = null;

    /**
     * Midgard templatedir to use with the request
     */
    public $templatedir_id = 0;

    /**
     * Path to the page used with the request
     */
    public $path = '/';

    private $prefix = '/';
    
    private $component = 'midgardmvc_core';

    private $path_for_page = array();

    /**
     * URL parameters after page has been resolved
     */
    public $argv = array();

    /**
     * Data associated with the request, typically set by a controller and displayed by a template
     */
    private $data = array();

    public function __construct()
    {
    }

    /**
     * Match an URL path to a page. Remaining path arguments are stored to argv
     *
     * @param $path URL path
     */
    public function resolve_node($path)
    {
        $node = midgardmvc_core::get_instance()->hierarchy->get_node_by_path($path);
        $this->set_node($node);
    }

    /**
     * Set a page to be used in the request
     */
    public function set_root_node(midgardmvc_core_providers_hierarchy_node $node)
    {
        $this->root_node = $node;
    }

    /**
     * Get root node used in this request
     */
    public function get_root_node()
    {
        return $this->root_node;
    }

    /**
     * Set a page to be used in the request
     */
    public function set_node(midgardmvc_core_providers_hierarchy_node $node)
    {
        $this->node = $node;
        $this->set_arguments($node->get_arguments());
        $this->set_component($node->get_component());
    }

    public function get_node()
    {
        return $this->node;
    }

    public function set_component($component)
    {
        if (!$component)
        {
            return;
        }
        $this->component = $component;
    }

    public function get_component()
    {
        return $this->component;
    }

    public function set_arguments(array $argv)
    {
        $this->argv = $argv;
    }
    
    public function get_arguments()
    {
        return $this->argv;
    }

    public function set_data_item($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function isset_data_item($key)
    {
        return isset($this->data[$key]);
    }

    public function get_data_item($key)
    {
        if (!isset($this->data[$key]))
        {
            // TODO: These are deprecated keys that used to be populated to context
            switch ($key)
            {
                case 'root_node':
                case 'root_page':
                    return $this->root_node;
                case 'component':
                    return $this->component;
                case 'uri':
                    return $this->path;
                case 'self':
                    return $this->path;
                case 'node':
                case 'page':
                    return $this->node;
                case 'prefix':
                    return $this->prefix;
                case 'argv':
                    return $this->argv;
                case 'query':
                    return $this->query;
                case 'request_method':
                    return $this->method;
                default:
                    throw new OutOfBoundsException("Midgard MVC request data '{$key}' not found.");
            }
        }
        return $this->data[$key];
    }

    public function get_data()
    {
        return $this->data;
    }

    public function set_method($method)
    {
        $this->method = strtolower($method);
    }
    
    public function get_method()
    {
        return $this->method;
    }
    
    public function set_query(array $get_params)
    {
        $this->query = $get_params;
    }
    
    public function get_query()
    {
        return $this->query;
    }

    public function set_prefix($prefix)
    {
        $this->prefix = $prefix;
    }
    
    public function get_prefix()
    {
        return $this->prefix;
    }

    /**
     * Generate a valid cache identifier for a context of the current request
     */
    public function generate_identifier()
    {
        $_core = midgardmvc_core::get_instance();
        if (isset($_core->context->cache_request_identifier))
        {
            // An injector has generated this already, let it be
            return;
        }

        $identifier_source  = "URI={$_core->context->uri}";
        $identifier_source .= ";COMP={$_core->context->component}";
        
        // TODO: Check language settings
        $identifier_source .= ';LANG=ALL';
        
        switch ($_core->context->cache_strategy)
        {
            case 'public':
                // Shared cache for everybody
                $identifier_source .= ';USER=EVERYONE';
                break;
            default:
                // Per-user cache
                if ($_core->authentication->is_user())
                {
                    $user = $_core->authentication->get_person();
                    $identifier_source .= ";USER={$user->username}";
                }
                else
                {
                    $identifier_source .= ';USER=ANONYMOUS';
                }
                break;
        }

        $_core->context->cache_request_identifier = md5($identifier_source);
    }
}
