<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */


  /**
   * The "moments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $plusService = new CFGS_Google_PlusMomentsService(...);
   *   $moments = $plusService->moments;
   *  </code>
   */
  class CFGS_Google_MomentsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Record a user activity (e.g Bill watched a video on Youtube) (moments.insert)
     *
     * @param string $userId The ID of the user to get activities for. The special value "me" can be used to indicate the authenticated user.
     * @param string $collection The collection to which to write moments.
     * @param CFGS_Google_Moment $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool debug Return the moment as written. Should be used only for debugging.
     * @return CFGS_Google_Moment
     */
    public function insert($userId, $collection, CFGS_Google_Moment $postBody, $optParams = array()) {
      $params = array('userId' => $userId, 'collection' => $collection, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Moment($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for CFGS_Google_Plus (v1moments).
 *
 * <p>
 * The Google+ API enables developers to build on top of the Google+ platform.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/+/history/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class CFGS_Google_PlusMomentsService extends CFGS_Google_Service {
  public $moments;
  /**
   * Constructs the internal representation of the Plus service.
   *
   * @param CFGS_Google_Client $client
   */
  public function __construct(CFGS_Google_Client $client) {
    $this->servicePath = 'plus/v1moments/people/';
    $this->version = 'v1moments';
    $this->serviceName = 'plus';

    $client->addService($this->serviceName, $this->version);
    $this->moments = new CFGS_Google_MomentsServiceResource($this, $this->serviceName, 'moments',
        json_decode('{"methods": {"insert": {"parameters": {"debug": {"type": "boolean", "location": "query"}, "userId": {"required": true, "type": "string", "location": "path"}, "collection": {"required": true, "type": "string", "location": "path", "enum": ["vault"]}}, "request": {"$ref": "Moment"}, "response": {"$ref": "Moment"}, "httpMethod": "POST", "path": "{userId}/moments/{collection}", "id": "plus.moments.insert"}}}', true));

  }
}

class CFGS_Google_ItemScope extends CFGS_Google_Model {
  public $startDate;
  public $endDate;
  public $text;
  public $image;
  protected $__addressType = 'CFGS_Google_ItemScope';
  protected $__addressDataType = '';
  public $address;
  public $birthDate;
  public $datePublished;
  public $addressLocality;
  public $duration;
  public $additionalName;
  public $worstRating;
  protected $__contributorType = 'CFGS_Google_ItemScope';
  protected $__contributorDataType = 'array';
  public $contributor;
  public $thumbnailUrl;
  public $id;
  public $postOfficeBoxNumber;
  protected $__attendeesType = 'CFGS_Google_ItemScope';
  protected $__attendeesDataType = 'array';
  public $attendees;
  protected $__authorType = 'CFGS_Google_ItemScope';
  protected $__authorDataType = 'array';
  public $author;
  protected $__associated_mediaType = 'CFGS_Google_ItemScope';
  protected $__associated_mediaDataType = 'array';
  public $associated_media;
  public $bestRating;
  public $addressCountry;
  public $width;
  public $streetAddress;
  protected $__locationType = 'CFGS_Google_ItemScope';
  protected $__locationDataType = '';
  public $location;
  public $latitude;
  protected $__byArtistType = 'CFGS_Google_ItemScope';
  protected $__byArtistDataType = '';
  public $byArtist;
  public $type;
  public $dateModified;
  public $contentSize;
  public $contentUrl;
  protected $__partOfTVSeriesType = 'CFGS_Google_ItemScope';
  protected $__partOfTVSeriesDataType = '';
  public $partOfTVSeries;
  public $description;
  public $familyName;
  public $kind;
  public $dateCreated;
  public $postalCode;
  public $attendeeCount;
  protected $__inAlbumType = 'CFGS_Google_ItemScope';
  protected $__inAlbumDataType = '';
  public $inAlbum;
  public $addressRegion;
  public $height;
  protected $__geoType = 'CFGS_Google_ItemScope';
  protected $__geoDataType = '';
  public $geo;
  public $embedUrl;
  public $tickerSymbol;
  public $playerType;
  protected $__aboutType = 'CFGS_Google_ItemScope';
  protected $__aboutDataType = '';
  public $about;
  public $givenName;
  public $name;
  protected $__performersType = 'CFGS_Google_ItemScope';
  protected $__performersDataType = 'array';
  public $performers;
  public $url;
  public $gender;
  public $longitude;
  protected $__thumbnailType = 'CFGS_Google_ItemScope';
  protected $__thumbnailDataType = '';
  public $thumbnail;
  public $caption;
  public $ratingValue;
  protected $__reviewRatingType = 'CFGS_Google_ItemScope';
  protected $__reviewRatingDataType = '';
  public $reviewRating;
  protected $__audioType = 'CFGS_Google_ItemScope';
  protected $__audioDataType = '';
  public $audio;
  public function setStartDate($startDate) {
    $this->startDate = $startDate;
  }
  public function getStartDate() {
    return $this->startDate;
  }
  public function setEndDate($endDate) {
    $this->endDate = $endDate;
  }
  public function getEndDate() {
    return $this->endDate;
  }
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
  public function setImage($image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setAddress(CFGS_Google_ItemScope $address) {
    $this->address = $address;
  }
  public function getAddress() {
    return $this->address;
  }
  public function setBirthDate($birthDate) {
    $this->birthDate = $birthDate;
  }
  public function getBirthDate() {
    return $this->birthDate;
  }
  public function setDatePublished($datePublished) {
    $this->datePublished = $datePublished;
  }
  public function getDatePublished() {
    return $this->datePublished;
  }
  public function setAddressLocality($addressLocality) {
    $this->addressLocality = $addressLocality;
  }
  public function getAddressLocality() {
    return $this->addressLocality;
  }
  public function setDuration($duration) {
    $this->duration = $duration;
  }
  public function getDuration() {
    return $this->duration;
  }
  public function setAdditionalName(/* array(CFGS_Google_string) */ $additionalName) {
    $this->assertIsArray($additionalName, 'CFGS_Google_string', __METHOD__);
    $this->additionalName = $additionalName;
  }
  public function getAdditionalName() {
    return $this->additionalName;
  }
  public function setWorstRating($worstRating) {
    $this->worstRating = $worstRating;
  }
  public function getWorstRating() {
    return $this->worstRating;
  }
  public function setContributor(/* array(CFGS_Google_ItemScope) */ $contributor) {
    $this->assertIsArray($contributor, 'CFGS_Google_ItemScope', __METHOD__);
    $this->contributor = $contributor;
  }
  public function getContributor() {
    return $this->contributor;
  }
  public function setThumbnailUrl($thumbnailUrl) {
    $this->thumbnailUrl = $thumbnailUrl;
  }
  public function getThumbnailUrl() {
    return $this->thumbnailUrl;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setPostOfficeBoxNumber($postOfficeBoxNumber) {
    $this->postOfficeBoxNumber = $postOfficeBoxNumber;
  }
  public function getPostOfficeBoxNumber() {
    return $this->postOfficeBoxNumber;
  }
  public function setAttendees(/* array(CFGS_Google_ItemScope) */ $attendees) {
    $this->assertIsArray($attendees, 'CFGS_Google_ItemScope', __METHOD__);
    $this->attendees = $attendees;
  }
  public function getAttendees() {
    return $this->attendees;
  }
  public function setAuthor(/* array(CFGS_Google_ItemScope) */ $author) {
    $this->assertIsArray($author, 'CFGS_Google_ItemScope', __METHOD__);
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
  public function setAssociated_media(/* array(CFGS_Google_ItemScope) */ $associated_media) {
    $this->assertIsArray($associated_media, 'CFGS_Google_ItemScope', __METHOD__);
    $this->associated_media = $associated_media;
  }
  public function getAssociated_media() {
    return $this->associated_media;
  }
  public function setBestRating($bestRating) {
    $this->bestRating = $bestRating;
  }
  public function getBestRating() {
    return $this->bestRating;
  }
  public function setAddressCountry($addressCountry) {
    $this->addressCountry = $addressCountry;
  }
  public function getAddressCountry() {
    return $this->addressCountry;
  }
  public function setWidth($width) {
    $this->width = $width;
  }
  public function getWidth() {
    return $this->width;
  }
  public function setStreetAddress($streetAddress) {
    $this->streetAddress = $streetAddress;
  }
  public function getStreetAddress() {
    return $this->streetAddress;
  }
  public function setLocation(CFGS_Google_ItemScope $location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }
  public function getLatitude() {
    return $this->latitude;
  }
  public function setByArtist(CFGS_Google_ItemScope $byArtist) {
    $this->byArtist = $byArtist;
  }
  public function getByArtist() {
    return $this->byArtist;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setDateModified($dateModified) {
    $this->dateModified = $dateModified;
  }
  public function getDateModified() {
    return $this->dateModified;
  }
  public function setContentSize($contentSize) {
    $this->contentSize = $contentSize;
  }
  public function getContentSize() {
    return $this->contentSize;
  }
  public function setContentUrl($contentUrl) {
    $this->contentUrl = $contentUrl;
  }
  public function getContentUrl() {
    return $this->contentUrl;
  }
  public function setPartOfTVSeries(CFGS_Google_ItemScope $partOfTVSeries) {
    $this->partOfTVSeries = $partOfTVSeries;
  }
  public function getPartOfTVSeries() {
    return $this->partOfTVSeries;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setFamilyName($familyName) {
    $this->familyName = $familyName;
  }
  public function getFamilyName() {
    return $this->familyName;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDateCreated($dateCreated) {
    $this->dateCreated = $dateCreated;
  }
  public function getDateCreated() {
    return $this->dateCreated;
  }
  public function setPostalCode($postalCode) {
    $this->postalCode = $postalCode;
  }
  public function getPostalCode() {
    return $this->postalCode;
  }
  public function setAttendeeCount($attendeeCount) {
    $this->attendeeCount = $attendeeCount;
  }
  public function getAttendeeCount() {
    return $this->attendeeCount;
  }
  public function setInAlbum(CFGS_Google_ItemScope $inAlbum) {
    $this->inAlbum = $inAlbum;
  }
  public function getInAlbum() {
    return $this->inAlbum;
  }
  public function setAddressRegion($addressRegion) {
    $this->addressRegion = $addressRegion;
  }
  public function getAddressRegion() {
    return $this->addressRegion;
  }
  public function setHeight($height) {
    $this->height = $height;
  }
  public function getHeight() {
    return $this->height;
  }
  public function setGeo(CFGS_Google_ItemScope $geo) {
    $this->geo = $geo;
  }
  public function getGeo() {
    return $this->geo;
  }
  public function setEmbedUrl($embedUrl) {
    $this->embedUrl = $embedUrl;
  }
  public function getEmbedUrl() {
    return $this->embedUrl;
  }
  public function setTickerSymbol($tickerSymbol) {
    $this->tickerSymbol = $tickerSymbol;
  }
  public function getTickerSymbol() {
    return $this->tickerSymbol;
  }
  public function setPlayerType($playerType) {
    $this->playerType = $playerType;
  }
  public function getPlayerType() {
    return $this->playerType;
  }
  public function setAbout(CFGS_Google_ItemScope $about) {
    $this->about = $about;
  }
  public function getAbout() {
    return $this->about;
  }
  public function setGivenName($givenName) {
    $this->givenName = $givenName;
  }
  public function getGivenName() {
    return $this->givenName;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setPerformers(/* array(CFGS_Google_ItemScope) */ $performers) {
    $this->assertIsArray($performers, 'CFGS_Google_ItemScope', __METHOD__);
    $this->performers = $performers;
  }
  public function getPerformers() {
    return $this->performers;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setGender($gender) {
    $this->gender = $gender;
  }
  public function getGender() {
    return $this->gender;
  }
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }
  public function getLongitude() {
    return $this->longitude;
  }
  public function setThumbnail(CFGS_Google_ItemScope $thumbnail) {
    $this->thumbnail = $thumbnail;
  }
  public function getThumbnail() {
    return $this->thumbnail;
  }
  public function setCaption($caption) {
    $this->caption = $caption;
  }
  public function getCaption() {
    return $this->caption;
  }
  public function setRatingValue($ratingValue) {
    $this->ratingValue = $ratingValue;
  }
  public function getRatingValue() {
    return $this->ratingValue;
  }
  public function setReviewRating(CFGS_Google_ItemScope $reviewRating) {
    $this->reviewRating = $reviewRating;
  }
  public function getReviewRating() {
    return $this->reviewRating;
  }
  public function setAudio(CFGS_Google_ItemScope $audio) {
    $this->audio = $audio;
  }
  public function getAudio() {
    return $this->audio;
  }
}

class CFGS_Google_Moment extends CFGS_Google_Model {
  public $startDate;
  public $kind;
  protected $__targetType = 'CFGS_Google_ItemScope';
  protected $__targetDataType = '';
  public $target;
  protected $__verbType = 'CFGS_Google_MomentVerb';
  protected $__verbDataType = '';
  public $verb;
  protected $__resultType = 'CFGS_Google_ItemScope';
  protected $__resultDataType = '';
  public $result;
  public $type;
  public function setStartDate($startDate) {
    $this->startDate = $startDate;
  }
  public function getStartDate() {
    return $this->startDate;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setTarget(CFGS_Google_ItemScope $target) {
    $this->target = $target;
  }
  public function getTarget() {
    return $this->target;
  }
  public function setVerb(CFGS_Google_MomentVerb $verb) {
    $this->verb = $verb;
  }
  public function getVerb() {
    return $this->verb;
  }
  public function setResult(CFGS_Google_ItemScope $result) {
    $this->result = $result;
  }
  public function getResult() {
    return $this->result;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class CFGS_Google_MomentVerb extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}
