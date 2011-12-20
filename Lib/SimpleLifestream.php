<?php
/**
 * SimpleLifestream.php
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

require(dirname(__FILE__) . '/SimpleLifestreamAdapter.php');
class SimpleLifestream
{
    protected $services = array();

    /**
     * Instantiates available services on construction.
     *
     * @param mixed $config You can pass an array with all the information
     *                      or a string with the location of a ini file with all the data.
     * @return void
     */
    public function __construct($config = array())
    {
        if (is_string($config) && is_readable($config))
            $config = parse_ini_file($config, true);

        if (!empty($config) && is_array($config))
        {
            foreach ($config as $serviceName => $values)
                $this->loadService($serviceName, $values);
        }
    }

    /**
     * Instantiates and initializes a service object!
     * It stores the object in the services property.
     *
     * @param string $serviceName
     * @param array $values an array with the service options
     * @return void
     */
    public function loadService($serviceName, $values)
    {
        $serviceName .= 'Service';
        if (!is_readable(dirname(__FILE__) . '/Services/' . $serviceName . '.php'))
            throw new Exception('The service ' . $serviceName . ' does not exist');

        require_once(dirname(__FILE__) . '/Services/' . $serviceName . '.php');
        $serviceObject = new $serviceName();
        $serviceObject->setConfig($values);
        $this->services[] = $serviceObject;
    }

    /**
     * Calls all available Services and gets all the
     * Api data and returns an array with the service name, date stamp and html for outputting.
     *
     * @param int $limit The maximal amount of entries you want to get.
     * @return array
     */
    public function getLifestream($limit = 0)
    {
        $output = array();
        if (empty($this->services))
            return $output;

        foreach ($this->services as $service)
            $output[] = $service->getApiData();

        $output = $this->flattenArray($output);

        if (!empty($output))
            usort($output, array($this, 'orderByDate'));

        if ($limit > 0 && count($output) > $limit)
            $output = array_slice($output, 0, $limit);

        return $output;
    }

    /**
     * flattens a multidimensional array
     *
     * @param array $array
     * @return array
     */
    protected function flattenArray($array)
    {
        $result = array();
        if (!is_array($array) || empty($array))
            return $result;

        foreach ($array as $value)
            $result = array_merge($result, $value);

        return array_filter($result);
    }

    /**
     * Callback method that organizes the stream by most recent
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    protected function orderByDate($a, $b) { return $a['date'] < $b['date']; }
}
?>