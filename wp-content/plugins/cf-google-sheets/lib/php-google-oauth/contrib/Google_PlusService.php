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
   * The "activities" collection of methods.
   * Typical usage is:
   *  <code>
   *   $plusService = new CFGS_Google_PlusService(...);
   *   $activities = $plusService->activities;
   *  </code>
   */
  class CFGS_Google_ActivitiesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Search public activities. (activities.search)
     *
     * @param string $query Full-text search query string.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string orderBy Specifies how to order search results.
     * @opt_param string pageToken The continuation token, used to page through large result sets. To get the next page of results, set this parameter to the value of "nextPageToken" from the previous response. This token may be of any length.
     * @opt_param string maxResults The maximum number of activities to include in the response, used for paging. For any response, the actual number returned may be less than the specified maxResults.
     * @opt_param string language Specify the preferred language to search with. See search language codes for available values.
     * @return CFGS_Google_ActivityFeed
     */
    public function search($query, $optParams = array()) {
      $params = array('query' => $query);
      $params = array_merge($params, $optParams);
      $data = $this->__call('search', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_ActivityFeed($data);
      } else {
        return $data;
      }
    }
    /**
     * List all of the activities in the specified collection for a particular user. (activities.list)
     *
     * @param string $userId The ID of the user to get activities for. The special value "me" can be used to indicate the authenticated user.
     * @param string $collection The collection of activities to list.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken The continuation token, used to page through large result sets. To get the next page of results, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of activities to include in the response, used for paging. For any response, the actual number returned may be less than the specified maxResults.
     * @return CFGS_Google_ActivityFeed
     */
    public function listActivities($userId, $collection, $optParams = array()) {
      $params = array('userId' => $userId, 'collection' => $collection);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_ActivityFeed($data);
      } else {
        return $data;
      }
    }
    /**
     * Get an activity. (activities.get)
     *
     * @param string $activityId The ID of the activity to get.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Activity
     */
    public function get($activityId, $optParams = array()) {
      $params = array('activityId' => $activityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Activity($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "comments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $plusService = new CFGS_Google_PlusService(...);
   *   $comments = $plusService->comments;
   *  </code>
   */
  class CFGS_Google_CommentsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * List all of the comments for an activity. (comments.list)
     *
     * @param string $activityId The ID of the activity to get comments for.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken The continuation token, used to page through large result sets. To get the next page of results, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of comments to include in the response, used for paging. For any response, the actual number returned may be less than the specified maxResults.
     * @opt_param string sortOrder The order in which to sort the list of comments.
     * @return CFGS_Google_CommentFeed
     */
    public function listComments($activityId, $optParams = array()) {
      $params = array('activityId' => $activityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommentFeed($data);
      } else {
        return $data;
      }
    }
    /**
     * Get a comment. (comments.get)
     *
     * @param string $commentId The ID of the comment to get.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Comment
     */
    public function get($commentId, $optParams = array()) {
      $params = array('commentId' => $commentId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Comment($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "people" collection of methods.
   * Typical usage is:
   *  <code>
   *   $plusService = new CFGS_Google_PlusService(...);
   *   $people = $plusService->people;
   *  </code>
   */
  class CFGS_Google_PeopleServiceResource extends CFGS_Google_ServiceResource {


    /**
     * List all of the people in the specified collection for a particular activity.
     * (people.listByActivity)
     *
     * @param string $activityId The ID of the activity to get the list of people for.
     * @param string $collection The collection of people to list.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken The continuation token, used to page through large result sets. To get the next page of results, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param string maxResults The maximum number of people to include in the response, used for paging. For any response, the actual number returned may be less than the specified maxResults.
     * @return CFGS_Google_PeopleFeed
     */
    public function listByActivity($activityId, $collection, $optParams = array()) {
      $params = array('activityId' => $activityId, 'collection' => $collection);
      $params = array_merge($params, $optParams);
      $data = $this->__call('listByActivity', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_PeopleFeed($data);
      } else {
        return $data;
      }
    }
    /**
     * Search all public profiles. (people.search)
     *
     * @param string $query Specify a query string for full text search of public text in all profiles.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken The continuation token, used to page through large result sets. To get the next page of results, set this parameter to the value of "nextPageToken" from the previous response. This token may be of any length.
     * @opt_param string maxResults The maximum number of people to include in the response, used for paging. For any response, the actual number returned may be less than the specified maxResults.
     * @opt_param string language Specify the preferred language to search with. See search language codes for available values.
     * @return CFGS_Google_PeopleFeed
     */
    public function search($query, $optParams = array()) {
      $params = array('query' => $query);
      $params = array_merge($params, $optParams);
      $data = $this->__call('search', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_PeopleFeed($data);
      } else {
        return $data;
      }
    }
    /**
     * Get a person's profile. (people.get)
     *
     * @param string $userId The ID of the person to get the profile for. The special value "me" can be used to indicate the authenticated user.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Person
     */
    public function get($userId, $optParams = array()) {
      $params = array('userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Person($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for CFGS_Google_Plus (v1).
 *
 * <p>
 * The Google+ API enables developers to build on top of the Google+ platform.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/+/api/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class CFGS_Google_PlusService extends CFGS_Google_Service {
  public $activities;
  public $comments;
  public $people;
  /**
   * Constructs the internal representation of the Plus service.
   *
   * @param CFGS_Google_Client $client
   */
  public function __construct(CFGS_Google_Client $client) {
    $this->servicePath = 'plus/v1/';
    $this->version = 'v1';
    $this->serviceName = 'plus';

    $client->addService($this->serviceName, $this->version);
    $this->activities = new CFGS_Google_ActivitiesServiceResource($this, $this->serviceName, 'activities', json_decode('{"methods": {"search": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"orderBy": {"default": "recent", "enum": ["best", "recent"], "type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "10", "maximum": "20", "minimum": "1", "location": "query", "type": "integer"}, "language": {"default": "", "type": "string", "location": "query"}, "query": {"required": true, "type": "string", "location": "query"}}, "id": "plus.activities.search", "httpMethod": "GET", "path": "activities", "response": {"$ref": "ActivityFeed"}}, "list": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "alt": {"default": "json", "enum": ["json"], "type": "string", "location": "query"}, "userId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"format": "uint32", "default": "20", "maximum": "100", "minimum": "1", "location": "query", "type": "integer"}, "collection": {"required": true, "type": "string", "location": "path", "enum": ["public"]}}, "id": "plus.activities.list", "httpMethod": "GET", "path": "people/{userId}/activities/{collection}", "response": {"$ref": "ActivityFeed"}}, "get": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}, "alt": {"default": "json", "enum": ["json"], "type": "string", "location": "query"}}, "id": "plus.activities.get", "httpMethod": "GET", "path": "activities/{activityId}", "response": {"$ref": "Activity"}}}}', true));
    $this->comments = new CFGS_Google_CommentsServiceResource($this, $this->serviceName, 'comments', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "activityId": {"required": true, "type": "string", "location": "path"}, "alt": {"default": "json", "enum": ["json"], "type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "20", "maximum": "100", "minimum": "0", "location": "query", "type": "integer"}, "sortOrder": {"default": "ascending", "enum": ["ascending", "descending"], "type": "string", "location": "query"}}, "id": "plus.comments.list", "httpMethod": "GET", "path": "activities/{activityId}/comments", "response": {"$ref": "CommentFeed"}}, "get": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"commentId": {"required": true, "type": "string", "location": "path"}}, "id": "plus.comments.get", "httpMethod": "GET", "path": "comments/{commentId}", "response": {"$ref": "Comment"}}}}', true));
    $this->people = new CFGS_Google_PeopleServiceResource($this, $this->serviceName, 'people', json_decode('{"methods": {"listByActivity": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "activityId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"format": "uint32", "default": "20", "maximum": "100", "minimum": "1", "location": "query", "type": "integer"}, "collection": {"required": true, "type": "string", "location": "path", "enum": ["plusoners", "resharers"]}}, "id": "plus.people.listByActivity", "httpMethod": "GET", "path": "activities/{activityId}/people/{collection}", "response": {"$ref": "PeopleFeed"}}, "search": {"scopes": ["https://www.googleapis.com/auth/plus.me"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "10", "maximum": "20", "minimum": "1", "location": "query", "type": "integer"}, "language": {"default": "", "type": "string", "location": "query"}, "query": {"required": true, "type": "string", "location": "query"}}, "id": "plus.people.search", "httpMethod": "GET", "path": "people", "response": {"$ref": "PeopleFeed"}}, "get": {"scopes": ["https://www.googleapis.com/auth/plus.me", "https://www.googleapis.com/auth/userinfo.email"], "parameters": {"userId": {"required": true, "type": "string", "location": "path"}}, "id": "plus.people.get", "httpMethod": "GET", "path": "people/{userId}", "response": {"$ref": "Person"}}}}', true));

  }
}

class CFGS_Google_Acl extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_PlusAclentryResource';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $description;
  public function setItems($items) {
    $this->assertIsArray($items, 'CFGS_Google_PlusAclentryResource', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}

class CFGS_Google_Activity extends CFGS_Google_Model {
  public $placeName;
  public $kind;
  public $updated;
  protected $__providerType = 'CFGS_Google_ActivityProvider';
  protected $__providerDataType = '';
  public $provider;
  public $title;
  public $url;
  public $geocode;
  protected $__objectType = 'CFGS_Google_ActivityObject';
  protected $__objectDataType = '';
  public $object;
  public $placeId;
  protected $__actorType = 'CFGS_Google_ActivityActor';
  protected $__actorDataType = '';
  public $actor;
  public $id;
  protected $__accessType = 'CFGS_Google_Acl';
  protected $__accessDataType = '';
  public $access;
  public $verb;
  public $etag;
  public $radius;
  public $address;
  public $crosspostSource;
  public $annotation;
  public $published;
  public function setPlaceName($placeName) {
    $this->placeName = $placeName;
  }
  public function getPlaceName() {
    return $this->placeName;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setProvider(CFGS_Google_ActivityProvider $provider) {
    $this->provider = $provider;
  }
  public function getProvider() {
    return $this->provider;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setGeocode($geocode) {
    $this->geocode = $geocode;
  }
  public function getGeocode() {
    return $this->geocode;
  }
  public function setObject(CFGS_Google_ActivityObject $object) {
    $this->object = $object;
  }
  public function getObject() {
    return $this->object;
  }
  public function setPlaceId($placeId) {
    $this->placeId = $placeId;
  }
  public function getPlaceId() {
    return $this->placeId;
  }
  public function setActor(CFGS_Google_ActivityActor $actor) {
    $this->actor = $actor;
  }
  public function getActor() {
    return $this->actor;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAccess(CFGS_Google_Acl $access) {
    $this->access = $access;
  }
  public function getAccess() {
    return $this->access;
  }
  public function setVerb($verb) {
    $this->verb = $verb;
  }
  public function getVerb() {
    return $this->verb;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setRadius($radius) {
    $this->radius = $radius;
  }
  public function getRadius() {
    return $this->radius;
  }
  public function setAddress($address) {
    $this->address = $address;
  }
  public function getAddress() {
    return $this->address;
  }
  public function setCrosspostSource($crosspostSource) {
    $this->crosspostSource = $crosspostSource;
  }
  public function getCrosspostSource() {
    return $this->crosspostSource;
  }
  public function setAnnotation($annotation) {
    $this->annotation = $annotation;
  }
  public function getAnnotation() {
    return $this->annotation;
  }
  public function setPublished($published) {
    $this->published = $published;
  }
  public function getPublished() {
    return $this->published;
  }
}

class CFGS_Google_ActivityActor extends CFGS_Google_Model {
  public $url;
  protected $__imageType = 'CFGS_Google_ActivityActorImage';
  protected $__imageDataType = '';
  public $image;
  public $displayName;
  public $id;
  protected $__nameType = 'CFGS_Google_ActivityActorName';
  protected $__nameDataType = '';
  public $name;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setImage(CFGS_Google_ActivityActorImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName(CFGS_Google_ActivityActorName $name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_ActivityActorImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_ActivityActorName extends CFGS_Google_Model {
  public $givenName;
  public $familyName;
  public function setGivenName($givenName) {
    $this->givenName = $givenName;
  }
  public function getGivenName() {
    return $this->givenName;
  }
  public function setFamilyName($familyName) {
    $this->familyName = $familyName;
  }
  public function getFamilyName() {
    return $this->familyName;
  }
}

class CFGS_Google_ActivityFeed extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  public $title;
  protected $__itemsType = 'CFGS_Google_Activity';
  protected $__itemsDataType = 'array';
  public $items;
  public $updated;
  public $nextLink;
  public $etag;
  public $id;
  public $selfLink;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'CFGS_Google_Activity', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_ActivityObject extends CFGS_Google_Model {
  protected $__resharersType = 'CFGS_Google_ActivityObjectResharers';
  protected $__resharersDataType = '';
  public $resharers;
  protected $__attachmentsType = 'CFGS_Google_ActivityObjectAttachments';
  protected $__attachmentsDataType = 'array';
  public $attachments;
  public $originalContent;
  protected $__plusonersType = 'CFGS_Google_ActivityObjectPlusoners';
  protected $__plusonersDataType = '';
  public $plusoners;
  protected $__actorType = 'CFGS_Google_ActivityObjectActor';
  protected $__actorDataType = '';
  public $actor;
  public $content;
  public $url;
  protected $__repliesType = 'CFGS_Google_ActivityObjectReplies';
  protected $__repliesDataType = '';
  public $replies;
  public $id;
  public $objectType;
  public function setResharers(CFGS_Google_ActivityObjectResharers $resharers) {
    $this->resharers = $resharers;
  }
  public function getResharers() {
    return $this->resharers;
  }
  public function setAttachments($attachments) {
    $this->assertIsArray($attachments, 'CFGS_Google_ActivityObjectAttachments', __METHOD__);
    $this->attachments = $attachments;
  }
  public function getAttachments() {
    return $this->attachments;
  }
  public function setOriginalContent($originalContent) {
    $this->originalContent = $originalContent;
  }
  public function getOriginalContent() {
    return $this->originalContent;
  }
  public function setPlusoners(CFGS_Google_ActivityObjectPlusoners $plusoners) {
    $this->plusoners = $plusoners;
  }
  public function getPlusoners() {
    return $this->plusoners;
  }
  public function setActor(CFGS_Google_ActivityObjectActor $actor) {
    $this->actor = $actor;
  }
  public function getActor() {
    return $this->actor;
  }
  public function setContent($content) {
    $this->content = $content;
  }
  public function getContent() {
    return $this->content;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setReplies(CFGS_Google_ActivityObjectReplies $replies) {
    $this->replies = $replies;
  }
  public function getReplies() {
    return $this->replies;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setObjectType($objectType) {
    $this->objectType = $objectType;
  }
  public function getObjectType() {
    return $this->objectType;
  }
}

class CFGS_Google_ActivityObjectActor extends CFGS_Google_Model {
  public $url;
  protected $__imageType = 'CFGS_Google_ActivityObjectActorImage';
  protected $__imageDataType = '';
  public $image;
  public $displayName;
  public $id;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setImage(CFGS_Google_ActivityObjectActorImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_ActivityObjectActorImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_ActivityObjectAttachments extends CFGS_Google_Model {
  public $displayName;
  protected $__fullImageType = 'CFGS_Google_ActivityObjectAttachmentsFullImage';
  protected $__fullImageDataType = '';
  public $fullImage;
  public $url;
  protected $__imageType = 'CFGS_Google_ActivityObjectAttachmentsImage';
  protected $__imageDataType = '';
  public $image;
  public $content;
  protected $__embedType = 'CFGS_Google_ActivityObjectAttachmentsEmbed';
  protected $__embedDataType = '';
  public $embed;
  public $id;
  public $objectType;
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setFullImage(CFGS_Google_ActivityObjectAttachmentsFullImage $fullImage) {
    $this->fullImage = $fullImage;
  }
  public function getFullImage() {
    return $this->fullImage;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setImage(CFGS_Google_ActivityObjectAttachmentsImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setContent($content) {
    $this->content = $content;
  }
  public function getContent() {
    return $this->content;
  }
  public function setEmbed(CFGS_Google_ActivityObjectAttachmentsEmbed $embed) {
    $this->embed = $embed;
  }
  public function getEmbed() {
    return $this->embed;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setObjectType($objectType) {
    $this->objectType = $objectType;
  }
  public function getObjectType() {
    return $this->objectType;
  }
}

class CFGS_Google_ActivityObjectAttachmentsEmbed extends CFGS_Google_Model {
  public $url;
  public $type;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class CFGS_Google_ActivityObjectAttachmentsFullImage extends CFGS_Google_Model {
  public $url;
  public $width;
  public $type;
  public $height;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setWidth($width) {
    $this->width = $width;
  }
  public function getWidth() {
    return $this->width;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setHeight($height) {
    $this->height = $height;
  }
  public function getHeight() {
    return $this->height;
  }
}

class CFGS_Google_ActivityObjectAttachmentsImage extends CFGS_Google_Model {
  public $url;
  public $width;
  public $type;
  public $height;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setWidth($width) {
    $this->width = $width;
  }
  public function getWidth() {
    return $this->width;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setHeight($height) {
    $this->height = $height;
  }
  public function getHeight() {
    return $this->height;
  }
}

class CFGS_Google_ActivityObjectPlusoners extends CFGS_Google_Model {
  public $totalItems;
  public $selfLink;
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_ActivityObjectReplies extends CFGS_Google_Model {
  public $totalItems;
  public $selfLink;
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_ActivityObjectResharers extends CFGS_Google_Model {
  public $totalItems;
  public $selfLink;
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_ActivityProvider extends CFGS_Google_Model {
  public $title;
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
}

class CFGS_Google_Comment extends CFGS_Google_Model {
  protected $__inReplyToType = 'CFGS_Google_CommentInReplyTo';
  protected $__inReplyToDataType = 'array';
  public $inReplyTo;
  public $kind;
  protected $__objectType = 'CFGS_Google_CommentObject';
  protected $__objectDataType = '';
  public $object;
  public $updated;
  protected $__actorType = 'CFGS_Google_CommentActor';
  protected $__actorDataType = '';
  public $actor;
  public $verb;
  public $etag;
  public $published;
  public $id;
  public $selfLink;
  public function setInReplyTo($inReplyTo) {
    $this->assertIsArray($inReplyTo, 'CFGS_Google_CommentInReplyTo', __METHOD__);
    $this->inReplyTo = $inReplyTo;
  }
  public function getInReplyTo() {
    return $this->inReplyTo;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setObject(CFGS_Google_CommentObject $object) {
    $this->object = $object;
  }
  public function getObject() {
    return $this->object;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setActor(CFGS_Google_CommentActor $actor) {
    $this->actor = $actor;
  }
  public function getActor() {
    return $this->actor;
  }
  public function setVerb($verb) {
    $this->verb = $verb;
  }
  public function getVerb() {
    return $this->verb;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setPublished($published) {
    $this->published = $published;
  }
  public function getPublished() {
    return $this->published;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_CommentActor extends CFGS_Google_Model {
  public $url;
  protected $__imageType = 'CFGS_Google_CommentActorImage';
  protected $__imageDataType = '';
  public $image;
  public $displayName;
  public $id;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setImage(CFGS_Google_CommentActorImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_CommentActorImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_CommentFeed extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  public $title;
  protected $__itemsType = 'CFGS_Google_Comment';
  protected $__itemsDataType = 'array';
  public $items;
  public $updated;
  public $nextLink;
  public $etag;
  public $id;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'CFGS_Google_Comment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_CommentInReplyTo extends CFGS_Google_Model {
  public $url;
  public $id;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_CommentObject extends CFGS_Google_Model {
  public $content;
  public $objectType;
  public function setContent($content) {
    $this->content = $content;
  }
  public function getContent() {
    return $this->content;
  }
  public function setObjectType($objectType) {
    $this->objectType = $objectType;
  }
  public function getObjectType() {
    return $this->objectType;
  }
}

class CFGS_Google_PeopleFeed extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  public $title;
  protected $__itemsType = 'CFGS_Google_Person';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $selfLink;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'CFGS_Google_Person', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_Person extends CFGS_Google_Model {
  public $relationshipStatus;
  protected $__organizationsType = 'CFGS_Google_PersonOrganizations';
  protected $__organizationsDataType = 'array';
  public $organizations;
  public $kind;
  public $displayName;
  protected $__nameType = 'CFGS_Google_PersonName';
  protected $__nameDataType = '';
  public $name;
  public $url;
  public $gender;
  public $aboutMe;
  public $tagline;
  protected $__urlsType = 'CFGS_Google_PersonUrls';
  protected $__urlsDataType = 'array';
  public $urls;
  protected $__placesLivedType = 'CFGS_Google_PersonPlacesLived';
  protected $__placesLivedDataType = 'array';
  public $placesLived;
  protected $__emailsType = 'CFGS_Google_PersonEmails';
  protected $__emailsDataType = 'array';
  public $emails;
  public $nickname;
  public $birthday;
  public $etag;
  protected $__imageType = 'CFGS_Google_PersonImage';
  protected $__imageDataType = '';
  public $image;
  public $hasApp;
  public $id;
  public $languagesSpoken;
  public $currentLocation;
  public $objectType;
  public function setRelationshipStatus($relationshipStatus) {
    $this->relationshipStatus = $relationshipStatus;
  }
  public function getRelationshipStatus() {
    return $this->relationshipStatus;
  }
  public function setOrganizations($organizations) {
    $this->assertIsArray($organizations, 'CFGS_Google_PersonOrganizations', __METHOD__);
    $this->organizations = $organizations;
  }
  public function getOrganizations() {
    return $this->organizations;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setName(CFGS_Google_PersonName $name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
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
  public function setAboutMe($aboutMe) {
    $this->aboutMe = $aboutMe;
  }
  public function getAboutMe() {
    return $this->aboutMe;
  }
  public function setTagline($tagline) {
    $this->tagline = $tagline;
  }
  public function getTagline() {
    return $this->tagline;
  }
  public function setUrls($urls) {
    $this->assertIsArray($urls, 'CFGS_Google_PersonUrls', __METHOD__);
    $this->urls = $urls;
  }
  public function getUrls() {
    return $this->urls;
  }
  public function setPlacesLived($placesLived) {
    $this->assertIsArray($placesLived, 'CFGS_Google_PersonPlacesLived', __METHOD__);
    $this->placesLived = $placesLived;
  }
  public function getPlacesLived() {
    return $this->placesLived;
  }
  public function setEmails($emails) {
    $this->assertIsArray($emails, 'CFGS_Google_PersonEmails', __METHOD__);
    $this->emails = $emails;
  }
  public function getEmails() {
    return $this->emails;
  }
  public function setNickname($nickname) {
    $this->nickname = $nickname;
  }
  public function getNickname() {
    return $this->nickname;
  }
  public function setBirthday($birthday) {
    $this->birthday = $birthday;
  }
  public function getBirthday() {
    return $this->birthday;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setImage(CFGS_Google_PersonImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setHasApp($hasApp) {
    $this->hasApp = $hasApp;
  }
  public function getHasApp() {
    return $this->hasApp;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setLanguagesSpoken($languagesSpoken) {
    $this->languagesSpoken = $languagesSpoken;
  }
  public function getLanguagesSpoken() {
    return $this->languagesSpoken;
  }
  public function setCurrentLocation($currentLocation) {
    $this->currentLocation = $currentLocation;
  }
  public function getCurrentLocation() {
    return $this->currentLocation;
  }
  public function setObjectType($objectType) {
    $this->objectType = $objectType;
  }
  public function getObjectType() {
    return $this->objectType;
  }
}

class CFGS_Google_PersonEmails extends CFGS_Google_Model {
  public $type;
  public $primary;
  public $value;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setPrimary($primary) {
    $this->primary = $primary;
  }
  public function getPrimary() {
    return $this->primary;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
}

class CFGS_Google_PersonImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_PersonName extends CFGS_Google_Model {
  public $honorificPrefix;
  public $middleName;
  public $familyName;
  public $formatted;
  public $givenName;
  public $honorificSuffix;
  public function setHonorificPrefix($honorificPrefix) {
    $this->honorificPrefix = $honorificPrefix;
  }
  public function getHonorificPrefix() {
    return $this->honorificPrefix;
  }
  public function setMiddleName($middleName) {
    $this->middleName = $middleName;
  }
  public function getMiddleName() {
    return $this->middleName;
  }
  public function setFamilyName($familyName) {
    $this->familyName = $familyName;
  }
  public function getFamilyName() {
    return $this->familyName;
  }
  public function setFormatted($formatted) {
    $this->formatted = $formatted;
  }
  public function getFormatted() {
    return $this->formatted;
  }
  public function setGivenName($givenName) {
    $this->givenName = $givenName;
  }
  public function getGivenName() {
    return $this->givenName;
  }
  public function setHonorificSuffix($honorificSuffix) {
    $this->honorificSuffix = $honorificSuffix;
  }
  public function getHonorificSuffix() {
    return $this->honorificSuffix;
  }
}

class CFGS_Google_PersonOrganizations extends CFGS_Google_Model {
  public $startDate;
  public $endDate;
  public $description;
  public $title;
  public $primary;
  public $location;
  public $department;
  public $type;
  public $name;
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
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setPrimary($primary) {
    $this->primary = $primary;
  }
  public function getPrimary() {
    return $this->primary;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setDepartment($department) {
    $this->department = $department;
  }
  public function getDepartment() {
    return $this->department;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_PersonPlacesLived extends CFGS_Google_Model {
  public $primary;
  public $value;
  public function setPrimary($primary) {
    $this->primary = $primary;
  }
  public function getPrimary() {
    return $this->primary;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
}

class CFGS_Google_PersonUrls extends CFGS_Google_Model {
  public $type;
  public $primary;
  public $value;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setPrimary($primary) {
    $this->primary = $primary;
  }
  public function getPrimary() {
    return $this->primary;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
}

class CFGS_Google_PlusAclentryResource extends CFGS_Google_Model {
  public $type;
  public $id;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}
