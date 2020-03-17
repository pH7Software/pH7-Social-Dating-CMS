<?php
/**
 * Copyright notice
 *
 * (c) 2013 Yohann CERDAN <cerdanyohann@yahoo.fr>
 * All rights reserved
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 *
 * ----------------- Modified by Pierre-Henry SORIA ----------------- *
 *
 * @author          Pierre-Henry SORIA <hello@ph7cms.com>
 * @copyright       (c) 2011-2019, Pierre-Henry Soria, All Rights Reserved.
 * @version         Last update 04/19/2018
 * @package         pH7CMS
 */

namespace PH7\Framework\Geo\Map;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Compress\Compress;
use PH7\Framework\Config\Config;

/**
 * Class to use the Google Maps v3 API
 *
 * @author Yohann CERDAN <cerdanyohann@yahoo.fr>
 * @author Pierre-Henry SORIA <hello@ph7cms.com>
 */
class Api
{
    const API_KEY_MIN_LENGTH = 10;
    const MARKER_ICON_PATH = PH7_URL_STATIC . PH7_IMG . 'icon/map-marker.svg';
    const GOOGLE_API_URL = 'https://maps.googleapis.com/maps/api/';
    const MARKER_CLUSTERER_LIBRARY_URL = 'https://cdnjs.cloudflare.com/ajax/libs/js-marker-clusterer/1.0.0/markerclusterer_compiled.js';

    /** GoogleMap ID for the HTML DIV and identifier for all the methods (to have several gmaps) **/
    protected $googleMapId = 'googlemapapi';

    /** GoogleMap  Direction ID for the HTML DIV **/
    protected $googleMapDirectionId = 'route';

    /** Width of the gmap **/
    protected $width = '';

    /** Height of the gmap **/
    protected $height = '';

    /** Icon width of the gmarker **/
    protected $iconWidth = 20;

    /** Icon height of the gmarker **/
    protected $iconHeight = 34;

    /** Icon width of the gmarker **/
    protected $iconAnchorWidth = 0;

    /** Icon height of the gmarker **/
    protected $iconAnchorHeight = 0;

    /** Infowindow width of the gmarker **/
    protected $infoWindowWidth = 250;

    /** Default zoom of the gmap **/
    protected $zoom = 9;

    /** Enable the zoom of the Infowindow **/
    protected $enableWindowZoom = false;

    /** Default zoom of the Infowindow **/
    protected $infoWindowZoom = 6;

    /** Lang of the gmap **/
    protected $lang = 'en';

    /** Google Map Key **/
    protected $key = '';

    /**Center of the gmap **/
    protected $center = 'New York United States';

    /** Content of the HTML generated **/
    protected $content = '';

    /** Add the direction button to the infowindow **/
    protected $displayDirectionFields = false;

    /** Hide the marker by default **/
    protected $defaultHideMarker = false;

    /** Extra content (marker, etc...) **/
    protected $contentMarker = '';

    /** Use clusterer to display a lot of markers on the gmap **/
    protected $useClusterer = false;
    protected $gridSize = 100;
    protected $maxZoom = 9;
    protected $clustererLibrarypath;

    /** Enable automatic center/zoom **/
    protected $enableAutomaticCenterZoom = false;

    /** maximum longitude of all markers **/
    protected $maxLng = -1000000;

    /** minimum longitude of all markers **/
    protected $minLng = 1000000;

    /** max latitude of all markers **/
    protected $maxLat = -1000000;

    /** min latitude of all markers **/
    protected $minLat = 1000000;

    /** map center latitude (horizontal), calculated automatically as markers are added to the map **/
    protected $centerLat = null;

    /** map center longitude (vertical),  calculated automatically as markers are added to the map **/
    protected $centerLng = null;

    /** factor by which to fudge the boundaries so that when we zoom encompass, the markers aren't too close to the edge **/
    protected $coordCoef = 0.01;

    /** Type of map to display **/
    protected $mapType = 'ROADMAP';

    /** Include the JS or not (if you have multiple maps **/
    protected $includeJs = true;

    /** Enable geolocation and center map **/
    protected $enableGeolocation = false;

    /** Compress JS Map file **/
    protected $bCompressor = false;


    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->bCompressor = (bool)Config::getInstance()->values['cache']['enable.static.minify'];
    }

    /**
     * Set the useClusterer parameter (optimization to display a lot of marker)
     *
     * @param boolean $useClusterer         use cluster or not
     * @param int     $gridSize             grid size (The grid size of a cluster in pixel.Each cluster will be a square.If you want the algorithm to run faster, you can set this value larger.The default value is 100.)
     * @param int     $maxZoom              maxZoom (The max zoom level monitored by a marker cluster.If not given, the marker cluster assumes the maximum map zoom level.When maxZoom is reached or exceeded all markers will be shown without cluster.)
     * @param string  $clustererLibraryPath clustererLibraryPath
     *
     * @return void
     */
    public function setClusterer($useClusterer, $gridSize = 100, $maxZoom = 9, $clustererLibraryPath = '')
    {
        $this->useClusterer = $useClusterer;
        $this->gridSize = $gridSize;
        $this->maxZoom = $maxZoom;
        $this->clustererLibraryPath = $clustererLibraryPath === '' ? self::MARKER_CLUSTERER_LIBRARY_URL : $clustererLibraryPath;
    }

    /**
     * Set the type of map, can be :
     * HYBRID, TERRAIN, ROADMAP, SATELLITE
     *
     * @param string $type
     *
     * @return void
     */
    public function setMapType($type)
    {
        $mapsType = ['ROADMAP', 'HYBRID', 'TERRAIN', 'SATELLITE'];

        if (!in_array(strtoupper($type), $mapsType, true)) {
            $this->mapType = $mapsType[0];
        } else {
            $this->mapType = strtoupper($type);
        }
    }

    /**
     * Set the ID of the default gmap DIV
     *
     * @param string $googleMapId the google div ID
     *
     * @return void
     */
    public function setDivId($googleMapId)
    {
        $this->googleMapId = $googleMapId;
    }

    /**
     * Set the ID of the default gmap direction DIV
     *
     * @param string $googleMapDirectionId GoogleMap  Direction ID for the HTML DIV
     *
     * @return void
     */
    public function setDirectionDivId($googleMapDirectionId)
    {
        $this->googleMapDirectionId = $googleMapDirectionId;
    }

    /**
     * Set the size of the gmap
     *
     * @param int $width GoogleMap  width
     * @param int $height GoogleMap  height
     *
     * @return void
     */
    public function setSize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getEnableGeolocation()
    {
        return $this->enableGeolocation;
    }

    /**
     * @param mixed $enableGeolocation
     */
    public function setEnableGeolocation($enableGeolocation)
    {
        $this->enableGeolocation = $enableGeolocation;
    }

    /**
     * Set the with of the gmap infowindow (on marker clik)
     *
     * @param int $infoWindowWidth GoogleMap  info window width
     *
     * @return void
     */
    public function setInfoWindowWidth($infoWindowWidth)
    {
        $this->infoWindowWidth = $infoWindowWidth;
    }

    /**
     * Set the size of the icon markers
     *
     * @param int $iconWidth GoogleMap  marker icon width
     * @param int $iconHeight GoogleMap  marker icon height
     *
     * @return void
     */
    public function setIconSize($iconWidth, $iconHeight)
    {
        $this->iconWidth = $iconWidth;
        $this->iconHeight = $iconHeight;
    }

    /**
     * Set the size of anchor icon markers
     *
     * @param int $iconAnchorWidth GoogleMap  anchor icon width
     * @param int $iconAnchorHeight GoogleMap  anchor icon height
     *
     * @return void
     */
    public function setIconAnchorSize($iconAnchorWidth, $iconAnchorHeight)
    {
        $this->iconAnchorWidth = $iconAnchorWidth;
        $this->iconAnchorHeight = $iconAnchorHeight;
    }

    /**
     * Set the lang of the gmap
     *
     * @param string $lang GoogleMap lang: fr, en, nl, ...
     *
     * @return void
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Set the Google Maps key
     * Ref: https://maps-apis.googleblog.com/2016/06/building-for-scale-updates-to-google.html
     *
     * @param string $key
     *
     * @return void
     */
    public function setKey($key)
    {
        $this->key = trim($key);
    }

    /**
     * Set the zoom of the gmap
     *
     * @param int $zoom GoogleMap  zoom.
     *
     * @return void
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * Set the zoom of the infowindow
     *
     * @param int $infoWindowZoom GoogleMap  zoom.
     *
     * @return void
     */
    public function setInfoWindowZoom($infoWindowZoom)
    {
        $this->infoWindowZoom = $infoWindowZoom;
    }

    /**
     * Enable the zoom on the marker when you click on it
     *
     * @param int $enableWindowZoom GoogleMap  zoom.
     *
     * @return void
     */
    public function setEnableWindowZoom($enableWindowZoom)
    {
        $this->enableWindowZoom = $enableWindowZoom;
    }

    /**
     * Enable theautomatic center/zoom at the gmap load
     *
     * @param int $enableAutomaticCenterZoom GoogleMap  zoom.
     *
     * @return void
     */
    public function setEnableAutomaticCenterZoom($enableAutomaticCenterZoom)
    {
        $this->enableAutomaticCenterZoom = $enableAutomaticCenterZoom;
    }

    /**
     * Set the center of the gmap (an address)
     *
     * @param string $center GoogleMap  center (an address)
     *
     * @return void
     */
    public function setCenter($center)
    {
        $this->center = $center;
    }

    /**
     * Set the center of the gmap (a lat and lng)
     *
     * @param string $lat
     * @param string $lng
     *
     * @return void
     */
    public function setCenterLatLng($lat, $lng)
    {
        $this->centerLatLng = 'new google.maps.LatLng(' . $lat . ', ' . $lng . ')';
    }

    /**
     * Set the center of the gmap
     *
     * @param boolean $displayDirectionFields display directions or not in the info window
     *
     * @return void
     */
    public function setDisplayDirectionFields($displayDirectionFields)
    {
        $this->displayDirectionFields = $displayDirectionFields;
    }

    /**
     * Set the defaultHideMarker
     *
     * @param boolean $defaultHideMarker hide all the markers on the map by default
     *
     * @return void
     */
    public function setDefaultHideMarker($defaultHideMarker)
    {
        $this->defaultHideMarker = $defaultHideMarker;
    }

    /**
     * Set the includeJs
     *
     * @param boolean $includeJs do not include JS
     *
     * @return void
     */
    public function setIncludeJs($includeJs)
    {
        $this->includeJs = $includeJs;
    }

    /**
     * Get the google map content
     *
     * @return string the google map html code
     */
    public function getMap()
    {
        if ($this->bCompressor) {
            $this->content = (new Compress)->parseJs($this->content);
        }

        return $this->content;
    }

    /**
     * Get URL content using cURL.
     *
     * @param string $url the url
     *
     * @return string the html code
     *
     * @todo add proxy settings
     */
    public function getContent($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    /**
     * Geocoding an address (address -> lat,lng)
     *
     * @param string $address an address
     *
     * @return array array with precision, lat & lng
     */
    public function geocoding($address)
    {
        $return = null;
        $url = self::GOOGLE_API_URL . 'geocode/json?address=' . urlencode($address) . '&amp;key=' . $this->key;

        if (function_exists('curl_init')) {
            $data = $this->getContent($url);
        } else {
            $data = file_get_contents($url);
        }

        $response = json_decode($data, true);

        if (!empty($response['status'])) {
            if ($response['status'] === 'OK') {
                $return = array(
                    $response['status'],
                    $response['results'][0]['types'],
                    $response['results'][0]['geometry']['location']['lat'],
                    $response['results'][0]['geometry']['location']['lng']
                );
            } else {
                echo '<!-- geocoding : failure to geocode : ' . $response['status'] . " -->\n";
            }
        } else {
            echo '<!-- geocoding : failure to reach ' . $url . " -->\n";
        }

        return $return;
    }

    /**
     * Add marker by his coord
     *
     * @param string $lat      lat
     * @param string $lng      lngs
     * @param string $title    title
     * @param string $html     html code display in the info window
     * @param string $category marker category
     * @param string $icon     an icon url
     *
     * @return void
     */
    public function addMarkerByCoords($lat, $lng, $title, $html = '', $category = '', $icon = '', $id = '')
    {
        if ($icon === '') {
            $icon = self::MARKER_ICON_PATH;
        }

        // Save the lat/lon to enable the automatic center/zoom
        $this->maxLng = (float)max((float)$lng, $this->maxLng);
        $this->minLng = (float)min((float)$lng, $this->minLng);
        $this->maxLat = (float)max((float)$lat, $this->maxLat);
        $this->minLat = (float)min((float)$lat, $this->minLat);
        $this->centerLng = (float)($this->minLng + $this->maxLng) / 2;
        $this->centerLat = (float)($this->minLat + $this->maxLat) / 2;

        $this->contentMarker .= "\t" . 'addMarker(new google.maps.LatLng("' . $lat . '","' . $lng . '"),"' . $title . '","' . $html . '","' . $category . '","' . $icon . '",map' . $this->googleMapId . ',"' . $id . '");' . "\n";
    }

    /**
     * Add marker by his address
     *
     * @param string $address  an address
     * @param string $title    title
     * @param string $content  html code display in the info window
     * @param string $category marker category
     * @param string $icon     an icon url
     *
     * @return void
     */
    public function addMarkerByAddress($address, $title = '', $content = '', $category = '', $icon = '', $id = '')
    {
        $point = $this->geocoding($address);
        if ($point !== null) {
            $this->addMarkerByCoords($point[2], $point[3], $title, $content, $category, $icon, $id);
        } else {
            echo '<!-- addMarkerByAddress : ADDRESS NOT FOUND ' . strip_tags($address) . " -->\n";
        }
    }

    /**
     * Add marker by an array of coord
     *
     * @param array $coordtab an array of lat,lng,content
     * @param string $category marker category
     * @param string $icon     an icon url
     *
     * @return void
     */
    public function addArrayMarkerByCoords($coordtab, $category = '', $icon = '')
    {
        foreach ($coordtab as $coord) {
            $this->addMarkerByCoords($coord[0], $coord[1], $coord[2], $coord[3], $category, $icon, (!empty($coord[4])) ? $coord[4] : '');
        }
    }

    /**
     * Add marker by an array of address
     *
     * @param array $coordtab an array of address
     * @param string $category marker category
     * @param string $icon     an icon url
     *
     * @return void
     */
    public function addArrayMarkerByAddress($coordtab, $category = '', $icon = '')
    {
        foreach ($coordtab as $coord) {
            $this->addMarkerByAddress($coord[0], $coord[1], $coord[2], $category, $icon, (!empty($coord[3])) ? $coord[3] : '');
        }
    }

    /**
     * Set a direction between 2 addresss and set a text panel
     *
     * @param string $from an address
     * @param string $to   an address
     *
     * @return void
     */
    public function addDirection($from, $to)
    {
        $this->contentMarker .= 'addDirection("' . $from . '","' . $to . '");';
    }

    /**
     * Parse a KML file and add markers to a category
     *
     * @param string $url      url of the kml file compatible with gmap and gearth
     * @param string $category marker category
     * @param string $icon     an icon url
     *
     * @return void
     */
    public function addKML($url, $category = '', $icon = '')
    {
        $xml = new SimpleXMLElement($url, null, true);
        foreach ($xml->Document->Folder->Placemark as $item) {
            $coordinates = explode(',', (string)$item->Point->coordinates);
            $name = (string)$item->name;
            $this->addMarkerByCoords($coordinates[1], $coordinates[0], $name, $name, $category, $icon);
        }
    }

    /**
     * Initialize the javascript code
     *
     * @return void
     */
    public function init()
    {
        // Google map DIV
        if (($this->width != '') && ($this->height != '')) {
            $this->content .= "\t" . '<div id="' . $this->googleMapId . '" style="width:' . $this->width . ';height:' . $this->height . '"></div>' . "\n";
        } else {
            $this->content .= "\t" . '<div id="' . $this->googleMapId . '"></div>' . "\n";
        }

        if ($this->includeJs === true) {
            // Google map JS
            $this->content .= '<script src="' . self::GOOGLE_API_URL . 'js?key=' .
                $this->key . '&amp;language=' . $this->lang . '">';
            $this->content .= '</script>' . "\n";

            // Clusterer JS
            if ($this->useClusterer) {
                $this->content .= '<script src="' . $this->clustererLibraryPath . '"></script>' . "\n";
            }
        }

        $this->content .= '<script>' . "\n";

        if ($this->isApiKeyNotSet()) {
            $this->content .= 'document.write("' . t('You need to get a Google Maps API key to get it working. Please go to your pH7CMS Admin Panel -> Settings -> General -> API -> Google Maps API Key') . '".toUpperCase());' . "\n";
        }

        $this->content .= 'function addLoadEvent(func) { ' . "\n";
        $this->content .= 'var oldonload = window.onload; ' . "\n";
        $this->content .= 'if (typeof window.onload != \'function\') { ' . "\n";
        $this->content .= 'window.onload = func; ' . "\n";
        $this->content .= '} else { ' . "\n";
        $this->content .= 'window.onload = function() { ' . "\n";
        $this->content .= 'if (oldonload) { ' . "\n";
        $this->content .= ' oldonload(); ' . "\n";
        $this->content .= '} ' . "\n";
        $this->content .= ' func(); ' . "\n";
        $this->content .= '} ' . "\n";
        $this->content .= '}' . "\n";
        $this->content .= '} ' . "\n";

        //global variables
        $this->content .= 'var geocoder = new google.maps.Geocoder();' . "\n";
        $this->content .= 'var map' . $this->googleMapId . ';' . "\n";
        $this->content .= 'var gmarkers = [];' . "\n";
        $this->content .= 'var infowindow;' . "\n";
        $this->content .= 'var directions = new google.maps.DirectionsRenderer();' . "\n";
        $this->content .= 'var directionsService = new google.maps.DirectionsService();' . "\n";
        $this->content .= 'var current_lat = 0;' . "\n";
        $this->content .= 'var current_lng = 0;' . "\n";

        // JS public function to get current Lat & Lng
        $this->content .= "\t" . 'function getCurrentLat() {' . "\n";
        $this->content .= "\t\t" . 'return current_lat;' . "\n";
        $this->content .= "\t" . '}' . "\n";
        $this->content .= "\t" . 'function getCurrentLng() {' . "\n";
        $this->content .= "\t\t" . 'return current_lng;' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to add a  marker
        $this->content .= "\t" . 'function addMarker(latlng,title,content,category,icon,currentmap,id) {' . "\n";
        $this->content .= "\t\t" . 'var marker = new google.maps.Marker({' . "\n";
        $this->content .= "\t\t\t" . 'map:  currentmap,' . "\n";
        $this->content .= "\t\t\t" . 'title : title,' . "\n";
        $this->content .= "\t\t\t" . 'icon:  {url:icon,size:new google.maps.Size(' . $this->iconWidth . ', ' . $this->iconHeight . ')';
        if (!empty($this->iconAnchorWidth) || !empty($this->iconAnchorHeight)) {
            $this->content .= ',anchor:new google.maps.Point(' . $this->iconAnchorWidth . ',' . $this->iconAnchorHeight . ')';
        }
        $this->content .= '},' . "\n";
        $this->content .= "\t\t\t" . 'position: latlng' . "\n";
        $this->content .= "\t\t" . '});' . "\n";

        // Display direction inputs in the info window
        if ($this->displayDirectionFields) {
            $this->content .= "\t\t" . 'content += \'<div style="clear:both;height:20px;"></div>\';' . "\n";
            $this->content .= "\t\t" . 'id_name = \'marker_\'+gmarkers.length;' . "\n";
            $this->content .= "\t\t" . 'content += \'<input type="text" id="\'+id_name+\'"/>\';' . "\n";
            $this->content .= "\t\t" . 'var from = ""+latlng.lat()+","+latlng.lng();' . "\n";
            $this->content .= "\t\t" . 'content += \'<br /><input type="button" onClick="addDirection(to.value,document.getElementById(\\\'\'+id_name+\'\\\').value);" value="Arrivée"/>\';' . "\n";
            $this->content .= "\t\t" . 'content += \'<input type="button" onClick="addDirection(document.getElementById(\\\'\'+id_name+\'\\\').value,to.value);" value="Départ"/>\';' . "\n";
        }

        $this->content .= "\t\t" . 'var html = \'<div style="text-align:left;width:' . $this->infoWindowWidth . 'px;" class="infoGmaps">\'+content+\'</div>\';' . "\n";
        $this->content .= "\t\t" . 'google.maps.event.addListener(marker, "click", function() {' . "\n";
        $this->content .= "\t\t\t" . 'if (infowindow) infowindow.close();' . "\n";
        $this->content .= "\t\t\t" . 'infowindow = new google.maps.InfoWindow({content: html,disableAutoPan: false});' . "\n";
        $this->content .= "\t\t\t" . 'infowindow.open(currentmap,marker);' . "\n";

        // Enable the zoom when you click on a marker
        if ($this->enableWindowZoom) {
            $this->content .= "\t\t\t" . 'currentmap.setCenter(new google.maps.LatLng(latlng.lat(),latlng.lng()),' . $this->infoWindowZoom . ');' . "\n";
        }

        $this->content .= "\t\t" . '});' . "\n";
        $this->content .= "\t\t" . 'marker.mycategory = category;' . "\n";
        $this->content .= "\t\t" . 'if (id) marker.id = id; else marker.id = \'marker_\'+gmarkers.length;' . "\n";
        $this->content .= "\t\t" . 'gmarkers.push(marker);' . "\n";

        // Hide marker by default
        if ($this->defaultHideMarker) {
            $this->content .= "\t\t" . 'marker.setVisible(false);' . "\n";
        }
        $this->content .= "\t" . '}' . "\n";

        // JS public function to add a geocode marker
        $this->content .= "\t" . 'function geocodeMarker(address,title,content,category,icon) {' . "\n";
        $this->content .= "\t\t" . 'if (geocoder) {' . "\n";
        $this->content .= "\t\t\t" . 'geocoder.geocode( { "address" : address}, function(results, status) {' . "\n";
        $this->content .= "\t\t\t\t" . 'if (status == google.maps.GeocoderStatus.OK) {' . "\n";
        $this->content .= "\t\t\t\t\t" . 'var latlng =  results[0].geometry.location;' . "\n";
        $this->content .= "\t\t\t\t\t" . 'addMarker(results[0].geometry.location,title,content,category,icon)' . "\n";
        $this->content .= "\t\t\t\t" . '}' . "\n";
        $this->content .= "\t\t\t" . '});' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to center the gmaps dynamically
        $this->content .= "\t" . 'function geocodeCenter(address) {' . "\n";
        $this->content .= "\t\t" . 'if (geocoder) {' . "\n";
        $this->content .= "\t\t\t" . 'geocoder.geocode( { "address": address}, function(results, status) {' . "\n";
        $this->content .= "\t\t\t\t" . 'if (status == google.maps.GeocoderStatus.OK) {' . "\n";
        $this->content .= "\t\t\t\t" . 'map' . $this->googleMapId . '.setCenter(results[0].geometry.location);' . "\n";
        $this->content .= "\t\t\t\t" . '} else {' . "\n";
        $this->content .= "\t\t\t\t" . 'console.error("' . t('Oops! Google Maps was not successful for the following reason:') . '" + status);' . "\n";
        $this->content .= "\t\t\t\t" . '}' . "\n";
        $this->content .= "\t\t\t" . '});' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to set direction
        $this->content .= "\t" . 'function addDirection(from,to) {' . "\n";
        $this->content .= "\t\t" . 'var request = {' . "\n";
        $this->content .= "\t\t" . 'origin:from, ' . "\n";
        $this->content .= "\t\t" . 'destination:to,' . "\n";
        $this->content .= "\t\t" . 'travelMode: google.maps.DirectionsTravelMode.DRIVING' . "\n";
        $this->content .= "\t\t" . '};' . "\n";
        $this->content .= "\t\t" . 'directionsService.route(request, function(response, status) {' . "\n";
        $this->content .= "\t\t" . 'if (status == google.maps.DirectionsStatus.OK) {' . "\n";
        $this->content .= "\t\t" . 'directions.setDirections(response);' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t\t" . '});' . "\n";

        $this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to show a marker
        $this->content .= "\t" . 'function showMarker(id) {' . "\n";
        $this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
        $this->content .= "\t\t\t" . 'if (gmarkers[i].id == id) {' . "\n";
        $this->content .= "\t\t\t\t" . ' google.maps.event.trigger(gmarkers[i],\'click\');' . "\n";
        $this->content .= "\t\t\t" . '}' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to show a category of marker
        $this->content .= "\t" . 'function showCategory(category) {' . "\n";
        $this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
        $this->content .= "\t\t\t" . 'if (gmarkers[i].mycategory == category) {' . "\n";
        $this->content .= "\t\t\t\t" . 'gmarkers[i].setVisible(true);' . "\n";
        $this->content .= "\t\t\t" . '}' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to hide a category of marker
        $this->content .= "\t" . 'function hideCategory(category) {' . "\n";
        $this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
        $this->content .= "\t\t\t" . 'if (gmarkers[i].mycategory == category) {' . "\n";
        $this->content .= "\t\t\t\t" . 'gmarkers[i].setVisible(false);' . "\n";
        $this->content .= "\t\t\t" . '}' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to hide all the markers
        $this->content .= "\t" . 'function hideAll() {' . "\n";
        $this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
        $this->content .= "\t\t\t" . 'gmarkers[i].setVisible(false);' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to show all the markers
        $this->content .= "\t" . 'function showAll() {' . "\n";
        $this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
        $this->content .= "\t\t\t" . 'gmarkers[i].setVisible(true);' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function to hide/show a category of marker - TODO BUG
        $this->content .= "\t" . 'function toggleHideShow(category) {' . "\n";
        $this->content .= "\t\t" . 'for (var i=0; i<gmarkers.length; i++) {' . "\n";
        $this->content .= "\t\t\t" . 'if (gmarkers[i].mycategory === category) {' . "\n";
        $this->content .= "\t\t\t\t" . 'if (gmarkers[i].getVisible()===true) { gmarkers[i].setVisible(false); }' . "\n";
        $this->content .= "\t\t\t\t" . 'else gmarkers[i].setVisible(true);' . "\n";
        $this->content .= "\t\t\t" . '}' . "\n";
        $this->content .= "\t\t" . '}' . "\n";
        $this->content .= "\t\t" . 'if(infowindow) { infowindow.close(); }' . "\n";
        $this->content .= "\t" . '}' . "\n";

        // JS public function add a KML
        $this->content .= "\t" . 'function addKML(file) {' . "\n";
        $this->content .= "\t\t" . 'var ctaLayer = new google.maps.KmlLayer(file);' . "\n";
        $this->content .= "\t\t" . 'ctaLayer.setMap(map' . $this->googleMapId . ');' . "\n";
        $this->content .= "\t" . '}' . "\n";
    }

    /**
     * Generate the HTML code of the gmap
     */
    public function generate()
    {
        $this->init();

        // init() function
        $this->content .= "\t" . 'function initialize' . $this->googleMapId . '() {' . "\n";
        $this->content .= "\t" . 'var defLatLng = new google.maps.LatLng(40.785091,-73.968285);' . "\n";
        $this->content .= "\t" . 'var defOptions = {' . "\n";
        $this->content .= "\t\t" . 'zoom: ' . $this->zoom . ',' . "\n";
        $this->content .= "\t\t" . 'center: defLatLng,' . "\n";
        $this->content .= "\t\t" . 'mapTypeId: google.maps.MapTypeId.' . $this->mapType . "\n";
        $this->content .= "\t" . '}' . "\n";

        // Google map Div ID
        $this->content .= "\t" . 'map' . $this->googleMapId . ' = new google.maps.Map(document.getElementById("' . $this->googleMapId . '"), defOptions);' . "\n";

        // Center
        if ($this->enableAutomaticCenterZoom) {
            $lenLng = $this->maxLng - $this->minLng;
            $lenLat = $this->maxLat - $this->minLat;
            $this->minLng -= $lenLng * $this->coordCoef;
            $this->maxLng += $lenLng * $this->coordCoef;
            $this->minLat -= $lenLat * $this->coordCoef;
            $this->maxLat += $lenLat * $this->coordCoef;

            $minLat = number_format(floatval($this->minLat), 12, '.', '');
            $minLng = number_format(floatval($this->minLng), 12, '.', '');
            $maxLat = number_format(floatval($this->maxLat), 12, '.', '');
            $maxLng = number_format(floatval($this->maxLng), 12, '.', '');
            $this->content .= "\t\t\t" . 'var bds = new google.maps.LatLngBounds(new google.maps.LatLng(' . $minLat . ',' . $minLng . '),new google.maps.LatLng(' . $maxLat . ',' . $maxLng . '));' . "\n";
            $this->content .= "\t\t\t" . 'map' . $this->googleMapId . '.fitBounds(bds);' . "\n";
        } else {
            if ($this->enableGeolocation === true) {
                $this->content .= "\t" . 'if(navigator.geolocation) {' . "\n";
                $this->content .= "\t\t" . 'navigator.geolocation.getCurrentPosition(function(position) {' . "\n";
                $this->content .= "\t\t" . 'var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);' . "\n";
                $this->content .= "\t\t" . 'map' . $this->googleMapId . '.setCenter(pos);' . "\n";
                $this->content .= "\t\t" . '},function() { geocodeCenter("' . $this->center . '"); } );' . "\n";
                $this->content .= "\t" . '}' . "\n";
            } elseif (isset($this->centerLatLng)) {
                $this->content .= "\t" . 'map' . $this->googleMapId . '.setCenter(' . $this->centerLatLng . ');' . "\n";
            } else {
                $this->content .= "\t" . 'geocodeCenter("' . $this->center . '");' . "\n";
            }
        }


        $this->content .= "\t" . 'google.maps.event.addListener(map' . $this->googleMapId . ',"click",function(event) { if (event) { current_lat=event.latLng.lat();current_lng=event.latLng.lng(); }}) ;' . "\n";

        $this->content .= "\t" . 'directions.setMap(map' . $this->googleMapId . ');' . "\n";
        $this->content .= "\t" . 'directions.setPanel(document.getElementById("' . $this->googleMapDirectionId . '"))' . "\n";

        // Add all the markers
        $this->content .= $this->contentMarker;

        // JS Clusterer
        if ($this->useClusterer) {
            $this->content .= "\t" . 'var markerCluster = new MarkerClusterer(map' . $this->googleMapId . ', gmarkers,{gridSize: ' . $this->gridSize . ', maxZoom: ' . $this->maxZoom . '});' . "\n";
        }

        $this->content .= '}' . "\n";

        // Loading the map at the end of the HTML
        $this->content .= "\t" . 'addLoadEvent(initialize' . $this->googleMapId . ');' . "\n";

        $this->content .= '</script>' . "\n";
    }

    /**
     * @return bool
     */
    public function isApiKeyNotSet()
    {
        return empty($this->key) || strlen($this->key) <= self::API_KEY_MIN_LENGTH;
    }
}
