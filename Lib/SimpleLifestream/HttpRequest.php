<?php
/**
 * HttpRequest.php
 *
 * @package SimpleLifestream
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleLifestream;

class HttpRequest Implements \SimpleLifestream\Interfaces\IHttp
{
    protected $config, $cache;

    /**
     * Constructor
     *
     * @param array $config
     * @param object $cache Instance of SimpleLifestream\Interfaces\ICache
     * @return void
     */
    public function __construct(array $config = array(), \SimpleLifestream\Interfaces\ICache $cache)
    {
        $config = array_intersect_key($config, array_flip(array('user_agent', 'timeout')));
        $this->config = array_merge(array(
            'user_agent' => 'PHP/SimpleLifestream',
            'timeout' => 0
        ), $config);

        $this->cache = $cache;
    }

    /**
     * Checks if the response from a url was already cached.
     * If that is not the case, it makes the request, stores the response
     * and returns the value.
     *
     * @param string $url
     * @param bool $uncompress
     * @return string
     */
    public function get($url, $uncompress = false)
    {
        $url = trim($url);
        $id = 'http_' . md5($url);

        $return = $this->cache->read($id);
        if (empty($return))
        {
            $return = $this->makeGetRequest($url, $uncompress);
            $this->cache->store($id, $return);
        }

        return $return;
    }

    /**
     * Sends an Oauth request
     *
     * @param string $string
     * @return void
     */
    public function oauth1Request($url, array $OauthData = array())
    {
        if (!class_exists('\Guzzle\Http\Client'))
            throw new \RuntimeException('You need to install Guzzle');

        $id = 'http_oauth_' . md5($url);
        $return = $this->cache->read($id);

        if (empty($return))
        {
            unset($OauthData['user']);

            $client = new \Guzzle\Http\Client($url);
            $oauth = new \Guzzle\Plugin\Oauth\OauthPlugin($OauthData);
            $client->addSubscriber($oauth);

            $response = $client->get()->send();
            $return = $response->getBody();

            $this->cache->store($id, $return);
        }

        return $return;
    }


    /**
     * Executes http requests
     *
     * @param string $url
     * @param bool $uncompress
     * @return string
     *
     * @throws RuntimeException when Guzzle or Curl are not available
     */
    protected function makeGetRequest($url, $uncompress = false)
    {
        if (function_exists('curl_init'))
        {
            if (class_exists('\Guzzle\Http\Client'))
            {
                $options = array(
                    CURLOPT_USERAGENT => $this->config['user_agent'],
                    CURLOPT_CONNECTTIMEOUT => $this->config['timeout']
                );

                $client = new \Guzzle\Http\Client($url, $options);
                $response = $client->get()->send();
                $response = $response->getBody();

                if ($uncompress && is_callable(array($response, 'uncompress')))
                    $response->uncompress();

                return (string) $response;
            }

            throw new \RuntimeException('You need to install Guzzle');
        }

        throw new \RuntimeException('You need to have curl installed');
    }
}

?>
