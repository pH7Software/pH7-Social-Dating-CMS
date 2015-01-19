<?php
namespace PH7\Framework\Geo\Map;
defined('PH7') or exit('Restricted access');
use PH7\Framework\Config\Config;

/*
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
*  @author            CERDAN Yohann <cerdanyohann@yahoo.fr>
*  @copyright      (c) 2011  CERDAN Yohann, All rights reserved
*  @ version         11/02/2011
*
* ----------------- Modification SORIA Pierre-Henry ----------------- *
*
* @author          SORIA Pierre-Henry <ph7software@gmail.com>
* @copyright       (c) 2011-2013, SORIA Pierre-Henry, All Rights Reserved.
* @version         Last update 08/30/2013
* @package         pH7 Dating CMS
*/

class MapAPI
{

    /** GoogleMap ID for the HTML DIV  **/
    protected $googleMapId = 'googlemapapi';

    /** GoogleMap  Direction ID for the HTML DIV **/
    protected $googleMapDirectionId = 'route';

    /** Width of the gmap **/
    protected $width = '';

    /** Height of the gmap **/
    protected $height = '';

    /** Icon width of the gmarker **/
    protected $iconWidth = 57;

    /** Icon height of the gmarker **/
    protected $iconHeight = 34;

    /** Infowindow width of the gmarker **/
    protected $infoWindowWidth = 250;

    /** Default zoom of the gmap **/
    protected $zoom = 9;

    /** Enable the zoom of the Infowindow **/
    protected $enableWindowZoom = false;

    /** Default zoom of the Infowindow **/
    protected $infoWindowZoom = 3;

    /** Lang of the gmap **/
    protected $lang = 'en';

    /**Center of the gmap **/
    protected $center = 'Wellington New Zealand';

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
    protected $clustererLibrarypath = '//google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer_packed.js';

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

    /** For openInfoWindowHtml **/
    protected $openInfoWindowHtml = null;

    /** Compress JS Map file **/
    protected $bCompressor = false;

    /** Display Information Bubble **/
    protected $bDisplayTextBubble = false;

    /**
     * Class constructor
     *
     * @return void
     */

    public function __construct()
    {
        $this->bCompressor = (bool)Config::getInstance()->values['cache']['enable.static.compressor'];
    }

    /**
     * Set the useClusterer parameter (optimization to display a lot of marker)
     *
     * @param boolean $useClusterer use cluster or not
     * @param int $gridSize grid size (The grid size of a cluster in pixel. Each cluster will be a square. If you want the algorithm to run faster, you can set this value larger. The default value is 100.)
     * @param int $maxZoom maxZoom (The max zoom level monitored by a marker cluster. If not given, the marker cluster assumes the maximum map zoom level. When maxZoom is reached or exceeded all markers will be shown without cluster.)
     * @param int $clustererLibraryPath clustererLibraryPath
     *
     * @return void
     */

    public function setClusterer($useClusterer, $gridSize = 100, $maxZoom = 9, $clustererLibraryPath = '')
    {
        $this->useClusterer = $useClusterer;
        $this->gridSize = $gridSize;
        $this->maxZoom = $maxZoom;
        ($clustererLibraryPath == '') ? $this->clustererLibraryPath = '//google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer_packed.js' : $clustererLibraryPath;
    }

    /**
     * Set the type of map, can be :
     * HYBRID, TERRAIN, ROADMAP, SATELLITE
     *
     * @param string $type
     * @return void
     */
    public function setMapType($type)
    {
        $mapsType = array('ROADMAP', 'HYBRID', 'TERRAIN', 'SATELLITE');
        if (!in_array(strtoupper($type), $mapsType)) {
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
     * Set the lang of the gmap
     *
     * @param string $lang GoogleMap  lang : fr,en,..
     *
     * @return void
     */

    public function setLang($lang)
    {
        $this->lang = $lang;
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
     * @param int $zoom GoogleMap  zoom.
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
     * @param int $zoom GoogleMap  zoom.
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
     * @param int $zoom GoogleMap  zoom.
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
     * Set Bubble Text
     *
     * @param boolean $bDisplay
     *
     * @return void
     */
    public function setDisplayTextBubble($bDisplay) {
        $this->bDisplayTextBubble = $bDisplay;
    }

    /**
     * Get the google map content
     *
     * @return string the google map html code
     */

    public function getMap()
    {
        if ($this->bCompressor)
            $this->content = (new \PH7\Framework\Compress\Compress)->parseJs($this->content);

        $returnContent = '';
        $returnContent .= '<script src="//maps.google.com/maps/api/js?sensor=false&amp;language='.$this->lang.'"></script>';
        // Clusterer JS
        if ($this->useClusterer == true)
            $returnContent .= '<script src="'.$this->clustererLibraryPath.'"></script>';

        $returnContent .= '<script>'.$this->content.'</script>';

        // Geo Map DIV
        $sStyle = (($this->width != '') && ($this->height != '')) ? ' style="width:'.$this->width.';height:'.$this->height.'" ' : ' ';

        $returnContent .= '<div id="'. $this->googleMapId.'"' . $sStyle . 'class="map"></div>';
        return $returnContent;
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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, $url);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    /**
     * Remove accentued characters
     *
     * @param string $chaine        The string to treat
     * @param string $remplace_par  The replacement character
     * @return string
     */
    public function withoutSpecialChars($text, $replaceBy = '_')
    {
        $accents = "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ";
        $sansAccents = "AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy";
        $text = strtr($text, $accents, $sansAccents);
        $text = preg_replace('/([^.a-z0-9]+)/i', $replaceBy, $text);
        return $text;
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
        $encodeAddress = urlencode($this->withoutSpecialChars($address));
        $url = '//maps.google.com/maps/geo?q=' . $encodeAddress . '&output=csv';

        if (function_exists('curl_init'))
            $data = $this->getContent($url);
        else
            $data = file_get_contents($url);

        $csvSplit = preg_split("/,/", $data);
        $status = $csvSplit[0];

        if (strcmp($status, "200") == 0) {
            $return = $csvSplit; // successful geocode, $precision = $csvSplit[1],$lat = $csvSplit[2],$lng = $csvSplit[3];
        } else {
            $this->content .= "<!-- geocoding : failure to geocode : " . $status . " -->\n";
            $return = null; // failure to geocode
        }

        return $return;
    }

    /**
     * Add marker by his coord
     *
     * @param string $lat lat
     * @param string $lng lngs
     * @param string $html html code display in the info window
     * @param string $category marker category
     * @param string $icon an icon url
     *
     * @return void
     */

    public function addMarkerByCoords($lat, $lng, $title, $html = '', $category = '', $icon = '')
    {
        if ($icon == '')
            $icon = '//maps.gstatic.com/intl/fr_ALL/mapfiles/markers/marker_sprite.png';

        // Save the lat/lon to enable the automatic center/zoom
        $this->maxLng = (float)max((float)$lng, $this->maxLng);
        $this->minLng = (float)min((float)$lng, $this->minLng);
        $this->maxLat = (float)max((float)$lat, $this->maxLat);
        $this->minLat = (float)min((float)$lat, $this->minLat);
        $this->centerLng = (float)($this->minLng + $this->maxLng) / 2;
        $this->centerLat = (float)($this->minLat + $this->maxLat) / 2;

        $this->contentMarker .= "\t" . 'addMarker(new google.maps.LatLng("' . $lat . '","' . $lng . '"),"' . $title . '","' . $html . '","' . $category . '","' . $icon . '");' . "\n";
    }

    /**
     * Add marker by his address
     *
     * @param string $address an ddress
     * @param string $content html code display in the info window
     * @param string $category marker category
     * @param string $icon an icon url
     *
     * @return void
     */

    public function addMarkerByAddress($address, $title = '', $content = '', $category = '', $icon = '')
    {
        $point = $this->geocoding($address);
        if ($point !== null) {
            $this->addMarkerByCoords($point[2], $point[3], $title, $content, $category, $icon);
        } else {
            $this->content .= "<!-- addMarkerByAddress : ADDRESS NOT FOUND " . strip_tags($address) . " -->\n";
            // throw new Exception('Adress not found : '.$address);
        }
    }

    /**
     * Add marker by an array of coord
     *
     * @param string $coordtab an array of lat,lng,content
     * @param string $category marker category
     * @param string $icon an icon url
     *
     * @return void
     */

    public function addArrayMarkerByCoords($coordtab, $category = '', $icon = '')
    {
        foreach ($coordtab as $coord) {
            $this->addMarkerByCoords($coord[0], $coord[1], $coord[2], $coord[3], $category, $icon);
        }
    }

    /**
     * Add marker by an array of address
     *
     * @param string $coordtab an array of address
     * @param string $category marker category
     * @param string $icon an icon url
     *
     * @return void
     */

    public function addArrayMarkerByAddress($coordtab, $category = '', $icon = '')
    {
        foreach ($coordtab as $coord) {
            $this->addMarkerByAddress($coord[0], $coord[1], $coord[2], $category, $icon);
        }
    }

    /**
     * Set a direction between 2 addresss and set a text panel
     *
     * @param string $from an address
     * @param string $to an address
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
     * @param string $url url of the kml file compatible with gmap and gearth
     * @param string $category marker category
     * @param string $icon an icon url
     *
     * @return void
     */

    public function addKML($url, $category = '', $icon = '')
    {
        $xml = new \SimpleXMLElement($url, null, true);
        foreach ($xml->Document->Folder->Placemark as $item) {
            $coordinates = explode(',', (string)$item->Point->coordinates);
            $name = (string)$item->name;
            $this->addMarkerByCoords($coordinates[1], $coordinates[0], $name, $name, $category, $icon);
        }
    }

    /**
     * Add the openInfoWindowHtml method in JS Map.
     *
     * @param int $point (mandatory)
     * @param string $string (optional) Optional only if the following parameters are used, otherwise it is mandatory.
     * @param string $link (optional)
     * @param string $icon (optional)
     * @param string $title (optional) For the "a" tag or "img" tag if they are used.
     * @param string $alt (optional)
     * @return void
     */

    public function addOpenInfoWindowHtml($point, $string = '', $link = '', $icon = '', $title = '', $alt = '')
    {
        if ($string !== '')
            $string .= '<br />';

        if ($link !== '')
        {
            $href = '<a href="' . $link . '"';

            if ($title !== '')
                $href .= ' title="' . $title . '"';

            $href .= '>';

            if ($icon === '')
                $href .= ($title !== '' ? $title : $link) . '</a>';
        }

        if ($icon !== '')
        {
            $img = '<br /><img src="' . $img . '" alt="' . $alt . '"';

            if ($title !== '')
                $img .= ' title="' . $title . '"';

            $img .= ' />';

            if ($link !== '')
                  $img .= '</a>';
        }

        $this->openInfoWindowHtml = '
        var point = new GLatLng(' . $point . ');
        var marker = new GMarker(point);
        var window = function() {
            marker.openInfoWindowHtml(' . $string . $href . $img . ');
        };
        GEvent.addListener(marker, "click", window);';
    }

    /**
     * Initialize the javascript code
     *
     * @return void
     */

    public function init()
    {
        //global variables
        $this->content .= 'var geocoder = new google.maps.Geocoder();' . "\n";
        $this->content .= 'var map;' . "\n";
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
        $this->content .= "\t" . 'function addMarker(latlng,title,content,category,icon) {' . "\n";
        $this->content .= "\t\t" . 'var marker = new google.maps.Marker({' . "\n";
        $this->content .= "\t\t\t" . 'map: map,' . "\n";
        $this->content .= "\t\t\t" . 'title : title,' . "\n";
        $this->content .= "\t\t\t" . 'icon:  new google.maps.MarkerImage(icon, new google.maps.Size(' . $this->iconWidth . ',' . $this->iconHeight . ')),' . "\n";
        $this->content .= "\t\t\t" . 'position: latlng' . "\n";
        $this->content .= "\t\t" . '});' . "\n";

        // Display direction inputs in the info window
        if ($this->displayDirectionFields) {
            $this->bDisplayTextBubble = true; // Set Text Bubble

            $this->content .= "\t\t" . 'content += \'<div style="clear:both;height:20px;"></div>\';' . "\n";
            $this->content .= "\t\t" . 'id_name = \'marker_\'+gmarkers.length;' . "\n";
            $this->content .= "\t\t" . 'content += \'<input type="text" id="\'+id_name+\'"/>\';' . "\n";
            $this->content .= "\t\t" . 'var from = ""+latlng.lat()+","+latlng.lng();' . "\n";
            $this->content .= "\t\t" . 'content += \'<br /><input type="button" onClick="addDirection(to.value,document.getElementById(\\\'\'+id_name+\'\\\').value);" value="Arrivée"/>\';' . "\n";
            $this->content .= "\t\t" . 'content += \'<input type="button" onClick="addDirection(document.getElementById(\\\'\'+id_name+\'\\\').value,to.value);" value="Départ"/>\';' . "\n";
        }

        if ($this->bDisplayTextBubble) {
            $this->content .= "\t\t" . 'var html = \'<div style="float:left;text-align:left;width:' . $this->infoWindowWidth . ';">\'+content+\'</div>\'' . "\n";
        }

        $this->content .= "\t\t" . 'google.maps.event.addListener(marker, "click", function() {' . "\n";
        $this->content .= "\t\t\t" . 'if (infowindow) infowindow.close();' . "\n";
        $this->content .= "\t\t\t" . 'infowindow = new google.maps.InfoWindow({content: html});' . "\n";
        $this->content .= "\t\t\t" . 'infowindow.open(map,marker);' . "\n";

        // Enable the zoom when you click on a marker
        if ($this->enableWindowZoom) {
            $this->content .= "\t\t" . 'map.setCenter(new google.maps.LatLng(latlng.lat(),latlng.lng()),' . $this->infoWindowZoom . ');' . "\n";
        }

        $this->content .= "\t\t" . '});' . "\n";
        $this->content .= "\t\t" . 'marker.mycategory = category;' . "\n";
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
        $this->content .= "\t\t\t\t" . 'map.setCenter(results[0].geometry.location);' . "\n";
        $this->content .= "\t\t\t\t" . '} else {' . "\n";
        $this->content .= "\t\t\t\t" . 'alert("' . t('Oops! Geo Map was not successful for the following reason:') . '" + status);' . "\n";
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
        $this->content .= "\t\t" . 'ctaLayer.setMap(map);' . "\n";
        $this->content .= "\t" . '}' . "\n";
        // openInfoWindowHtml
        if (!empty($this->openInfoWindowHtml))
            $this->content .= "\t" . $this->openInfoWindowHtml;
    }

    public function generate()
    {
        $this->init();

        //Fonction init()
        $this->content .= "\t" . 'function initialize() {' . "\n";
        $this->content .= "\t" . 'var myLatlng = new google.maps.LatLng(48.8792,2.34778);' . "\n";
        $this->content .= "\t" . 'var myOptions = {' . "\n";
        $this->content .= "\t\t" . 'zoom: ' . $this->zoom . ',' . "\n";
        $this->content .= "\t\t" . 'center: myLatlng,' . "\n";
        $this->content .= "\t\t" . 'mapTypeId: google.maps.MapTypeId.' . $this->mapType . "\n";
        $this->content .= "\t" . '}' . "\n";

        //Goole map Div Id
        $this->content .= "\t" . 'map = new google.maps.Map(document.getElementById("' . $this->googleMapId . '"), myOptions);' . "\n";

        // Center
        if ($this->enableAutomaticCenterZoom == true) {
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
            $this->content .= "\t\t\t" . 'map.fitBounds(bds);' . "\n";
        } else {
            $this->content .= "\t" . 'geocodeCenter("' . $this->center . '");' . "\n";
        }

        $this->content .= "\t" . 'google.maps.event.addListener(map,"click",function(event) { if (event) { current_lat=event.latLng.lat();current_lng=event.latLng.lng(); }}) ;' . "\n";

        $this->content .= "\t" . 'directions.setMap(map);' . "\n";
        $this->content .= "\t" . 'directions.setPanel(document.getElementById("' . $this->googleMapDirectionId . '"))' . "\n";

        // add all the markers
        $this->content .= $this->contentMarker;

        // Clusterer JS
        if ($this->useClusterer == true) {
            $this->content .= "\t" . 'var markerCluster = new MarkerClusterer(map, gmarkers,{gridSize: ' . $this->gridSize . ', maxZoom: ' . $this->maxZoom . '});' . "\n";
        }

        $this->content .= '}' . "\n";

        // Chargement de la map a la fin du HTML
        $this->content .= "\t" . 'window.onload=initialize;' . "\n";

    }

}
