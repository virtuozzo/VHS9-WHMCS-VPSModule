<?php

namespace OnAppVps\Database;

use Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Database\Schema\Blueprint;

class LocationGroups extends Database
{

    protected $table    = 'onappVPS_LocationGroups';
    protected $builder;
    public    $fillable = ['id', 'city', 'country', 'location_id', 'federated', 'latlng'];

    /**
     * Update all locations in database from onapp
     *
     * @param $locations
     */
    public function saveLocationsFromOnApp($locations)
    {
        foreach ($locations as $item) {
            $location = $item['location_group'];
            $lg       = $this->getLocationGroupForLocation($location);
            $lg->fill([
                'city'        => $location['city'],
                'country'     => $location['country'],
                'location_id' => $location['id'],
                'federated'   => $location['federated'],
            ]);
            $lg->latlng = $this->getLatlngFromGoogle($lg);
            if($lg->latlng == NULL)
            {
                $lg->latlng = ' ';
            }
            
            $lg->save();
        }
    }

    /**
     * If such location already exists, selects it and return to update
     * otherwise creates new object of LocationGroups
     *
     * @param $location
     * @return LocationGroups
     */
    public function getLocationGroupForLocation($location)
    {
        $lg = new LocationGroups();
        $query = $lg->where('city', $location['city'])->where('country', $location['country'])->where('location_id', $location['id']);

        return ($query->count() > 0)? $query->first():new LocationGroups();
    }

    /**
     * Obtain longitude and latitude based on city and country
     * name from LocationGroups object
     *
     * @param LocationGroups $location
     * @return string
     */
    private function getLatlngFromGoogle(LocationGroups $location)
    {
        $url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s,%s&key=AIzaSyDL1GymTC0KbZQrsy3wRJoyivelJjjMW6E',
            $location->country, $location->city);

        $geocurl = curl_init();
        curl_setopt($geocurl, CURLOPT_URL, str_replace(' ', '+', $url));
        curl_setopt($geocurl, CURLOPT_HEADER, 0); //Change this to a 1 to return headers
        curl_setopt($geocurl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($geocurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($geocurl, CURLOPT_RETURNTRANSFER, 1);

        $geofile = curl_exec($geocurl);
        $result  = json_decode($geofile, true);
        if (!empty($result) && isset($result['results'])) {
            $first = reset($result['results']);
            $loc   = $first['geometry']['location'];

            return implode(',', $loc);
        }

        return '';
    }

    public function getMatchingConfigurableOption($configurableId)
    {
        $optionSub = new ProductConfigOptionsSub();
        $query = $optionSub->where('configid', $configurableId)->where('optionname', 'like', $this->location_id.'|'.$this->city);
        if($query->count() == 0) {
            return null;
        }
        $id = $query->get(['id'])->first()->toArray();
        return !empty($id)? $id['id']:null;
    }

    public function getFullCityAttribute()
    {
        return ($this->federated)? $this->city.' (federated)':$this->city;
    }

}
