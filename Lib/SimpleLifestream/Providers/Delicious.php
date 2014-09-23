<?php
/**
 * Delicious.php
 *
 * @package Providers
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Providers;

/**
 * A provider for Delicious
 */
class Delicious extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'http://feeds.delicious.com/v2/json/%s?count=20';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response)) {
            return array_map(array($this, 'filterResponse'), $response);
        }

        return null;
    }

    /**
     * Filters and formats the response
     *
     * @param array $value
     * @return array
     */
    protected function filterResponse(array $value = array())
    {
        $callbackReturn = $this->applyCallbacks($value);
        return array_merge($callbackReturn, array(
            'service'  => 'delicious',
            'type'     => 'bookmarked',
            'resource' => $value['a'],
            'stamp'    => strtotime($value['dt']),
            'url'      => $value['u'],
            'text'     => $value['d'],
        ));
    }
}
?>
