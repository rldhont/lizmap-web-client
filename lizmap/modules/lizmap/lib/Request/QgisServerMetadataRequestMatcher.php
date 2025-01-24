<?php
/**
 * Manage OGC response.
 *
 * @author    3liz
 * @copyright 2015-2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Request;

use Kevinrob\GuzzleCache\Strategy\Delegate\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;

class QgisServerMetadataRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var string
     */
    protected $qgisServerHost;

    public function __construct(string $wmsServerUrl)
    {
        $urlInfo = parse_url($wmsServerUrl);
        $this->qgisServerHost = isset($urlInfo['host']) ? $urlInfo['host'] : 'localhost';
    }

    public function matches(RequestInterface $request)
    {
        return strpos($request->getUri()->getHost(), $this->qgisServerHost) !== false
        && substr($request->getUri()->getPath(), -12) === '/server.json';
    }
}
