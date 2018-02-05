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
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $activities = $youtubeService->activities;
   *  </code>
   */
  class CFGS_Google_ActivitiesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Post a channel bulletin. (activities.insert)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_Activity $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_Activity
     */
    public function insert($part, CFGS_Google_Activity $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Activity($data);
      } else {
        return $data;
      }
    }
    /**
     * Browse the YouTube channel activity collection. (activities.list)
     *
     * @param string $part Activity parts to include in the returned response. Valid values are: id, snippet and contentDetails.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string publishedBefore Only return activities published before given date (exclusive).
     * @opt_param string channelId YouTube ID of the channel.
     * @opt_param string mine Flag indicating to return user's activities.
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string pageToken Token for the page selection.
     * @opt_param string home Flag indicating to return user's homepage feed.
     * @opt_param string publishedAfter Only return activities published after given date (inclusive).
     * @return CFGS_Google_ActivityListResponse
     */
    public function listActivities($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_ActivityListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "search" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $search = $youtubeService->search;
   *  </code>
   */
  class CFGS_Google_SearchServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Universal search for youtube. (search.list)
     *
     * @param string $part Search result parts to include in the returned response. Valid values are: id and snippet.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string topicId Only search for resources with the specified topic
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string videoDuration Add a filter on the duration of the videos.
     * @opt_param string videoDimension Add a filter for the number of dimensions in the videos.
     * @opt_param string videoLicense Add a filter on the licensing of the videos.
     * @opt_param string maxResults Maximum number of search results to return per page.
     * @opt_param string videoCaption Add a filter on the the presence of captions on the videos.
     * @opt_param string q Query to search in Youtube.
     * @opt_param string pageToken Token for the page selection.
     * @opt_param string videoDefinition Add a filter for the definition of the videos.
     * @opt_param string published Only search for resources uploaded at a specific pediod
     * @opt_param string relatedToVideo Search for resources related to this video. Need to be used with type set to 'video'
     * @opt_param string type Type of resource to search.
     * @opt_param string order Sort order.
     * @return CFGS_Google_SearchListResponse
     */
    public function listSearch($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_SearchListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "guideCategories" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $guideCategories = $youtubeService->guideCategories;
   *  </code>
   */
  class CFGS_Google_GuideCategoriesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Browse the YouTube guide category collection. (guideCategories.list)
     *
     * @param string $part Guide category parts to include in the returned response. Valid values are: id and snippet.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string regionCode Return the channelCategories in the given region code.
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string id Comma-separated YouTube IDs of the channelCategories to be returned.
     * @opt_param string hl Language for the returned channelCategories.
     * @return CFGS_Google_GuideCategoryListResponse
     */
    public function listGuideCategories($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_GuideCategoryListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "playlists" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $playlists = $youtubeService->playlists;
   *  </code>
   */
  class CFGS_Google_PlaylistsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Create a playlist. (playlists.insert)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_Playlist $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_Playlist
     */
    public function insert($part, CFGS_Google_Playlist $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Playlist($data);
      } else {
        return $data;
      }
    }
    /**
     * Browse the YouTube playlist collection. (playlists.list)
     *
     * @param string $part Playlist parts to include in the returned response. Valid values are: id, snippet and status.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string mine Flag indicating only return the playlists of the authenticated user.
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string pageToken Token for the page selection.
     * @opt_param string id Comma-separated YouTube IDs of the playlists to be returned.
     * @return CFGS_Google_PlaylistListResponse
     */
    public function listPlaylists($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_PlaylistListResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a playlist. (playlists.update)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_Playlist $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_Playlist
     */
    public function update($part, CFGS_Google_Playlist $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Playlist($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes playlists by IDs. (playlists.delete)
     *
     * @param string $id YouTube IDs of the playlists to be deleted.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     */
    public function delete($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "videos" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $videos = $youtubeService->videos;
   *  </code>
   */
  class CFGS_Google_VideosServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Upload a video to YouTube. (videos.insert)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_Video $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_Video
     */
    public function insert($part, CFGS_Google_Video $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Video($data);
      } else {
        return $data;
      }
    }
    /**
     * Browse the YouTube video collection. (videos.list)
     *
     * @param string $id YouTube IDs of the videos to be returned.
     * @param string $part Video parts to include in the returned response. Valid values are: id, snippet, contentDetails, player, statistics, status and topicDetails.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_VideoListResponse
     */
    public function listVideos($id, $part, $optParams = array()) {
      $params = array('id' => $id, 'part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_VideoListResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a video. (videos.update)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_Video $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_Video
     */
    public function update($part, CFGS_Google_Video $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Video($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete a YouTube video. (videos.delete)
     *
     * @param string $id YouTube ID of the video to be deleted.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     */
    public function delete($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "subscriptions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $subscriptions = $youtubeService->subscriptions;
   *  </code>
   */
  class CFGS_Google_SubscriptionsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Insert a subscription. (subscriptions.insert)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_Subscription $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_Subscription
     */
    public function insert($part, CFGS_Google_Subscription $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Subscription($data);
      } else {
        return $data;
      }
    }
    /**
     * Browses the subscriptions collection. (subscriptions.list)
     *
     * @param string $part Subscription parts to include in the returned response. Valid values are: id, snippet and contentDetails.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string channelId Only return subscriptions to given channelId.
     * @opt_param string mine Flag indicating only return the subscriptions of the authenticated user.
     * @opt_param string maxResults Maximum number of search results to return per page.
     * @opt_param string forChannelId Comma separated list of channel IDs to return subscriptions for.
     * @opt_param string pageToken Token for the page selection.
     * @opt_param string order Sort order.
     * @opt_param string id YouTube IDs of the subscriptions to be returned.
     * @return CFGS_Google_SubscriptionListResponse
     */
    public function listSubscriptions($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_SubscriptionListResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes subscriptions by IDs. (subscriptions.delete)
     *
     * @param string $id YouTube IDs of the subscription to be deleted.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     */
    public function delete($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "channels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $channels = $youtubeService->channels;
   *  </code>
   */
  class CFGS_Google_ChannelsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Browse the YouTube channel collection. Either the 'id' or 'mine' parameter must be set.
     * (channels.list)
     *
     * @param string $part Channel parts to include in the returned response. Valid values are: id, snippet, contentDetails and topicDetails.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string mine Filter to only channels owned by authenticated user.
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string id YouTube IDs of the channels to be returned.
     * @opt_param string pageToken Token for the page selection.
     * @opt_param string mySubscribers Filter to channels that subscribed to the channel of the authenticated user.
     * @opt_param string categoryId Filter to retrieve the channels within the given category ID.
     * @return CFGS_Google_ChannelListResponse
     */
    public function listChannels($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_ChannelListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "playlistItems" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $playlistItems = $youtubeService->playlistItems;
   *  </code>
   */
  class CFGS_Google_PlaylistItemsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Insert a resource into a playlist. (playlistItems.insert)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_PlaylistItem $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_PlaylistItem
     */
    public function insert($part, CFGS_Google_PlaylistItem $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_PlaylistItem($data);
      } else {
        return $data;
      }
    }
    /**
     * Browse the YouTube playlist collection. (playlistItems.list)
     *
     * @param string $part Playlist item parts to include in the returned response. Valid values are: id, snippet and contentDetails.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string playlistId Retrieves playlist items from the given playlist id.
     * @opt_param string maxResults Maximum number of results to return
     * @opt_param string pageToken Token for the page selection.
     * @opt_param string id YouTube IDs of the playlist items to be returned.
     * @return CFGS_Google_PlaylistItemListResponse
     */
    public function listPlaylistItems($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_PlaylistItemListResponse($data);
      } else {
        return $data;
      }
    }
    /**
     * Update a playlist item. (playlistItems.update)
     *
     * @param string $part One or more parts to return on the current request.
     * @param CFGS_Google_PlaylistItem $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @return CFGS_Google_PlaylistItem
     */
    public function update($part, CFGS_Google_PlaylistItem $postBody, $optParams = array()) {
      $params = array('part' => $part, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_PlaylistItem($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes playlist items by IDs. (playlistItems.delete)
     *
     * @param string $id YouTube IDs of the playlist items to be deleted.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     */
    public function delete($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "videoCategories" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new CFGS_Google_YouTubeService(...);
   *   $videoCategories = $youtubeService->videoCategories;
   *  </code>
   */
  class CFGS_Google_VideoCategoriesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Browse list of video categories. (videoCategories.list)
     *
     * @param string $part Video category parts to include in the returned response. Valid values are: id and snippet.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string regionCode Return all the categories in this region.
     * @opt_param string onBehalfOfContentOwner The authenticated user acts on behalf of this content owner.
     * @opt_param string id IDs of the categories to be returned.
     * @opt_param string hl Language used for the title of the categories.
     * @return CFGS_Google_VideoCategoryListResponse
     */
    public function listVideoCategories($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_VideoCategoryListResponse($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for CFGS_Google_YouTube (v3).
 *
 * <p>
 * Programmatic access to YouTube features.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/youtube" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class CFGS_Google_YouTubeService extends CFGS_Google_Service {
  public $activities;
  public $search;
  public $guideCategories;
  public $playlists;
  public $videos;
  public $subscriptions;
  public $channels;
  public $playlistItems;
  public $videoCategories;
  /**
   * Constructs the internal representation of the YouTube service.
   *
   * @param CFGS_Google_Client $client
   */
  public function __construct(CFGS_Google_Client $client) {
    $this->servicePath = 'youtube/v3/';
    $this->version = 'v3';
    $this->serviceName = 'youtube';

    $client->addService($this->serviceName, $this->version);
    $this->activities = new CFGS_Google_ActivitiesServiceResource($this, $this->serviceName, 'activities', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/youtube"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "Activity"}, "response": {"$ref": "Activity"}, "httpMethod": "POST", "path": "activities", "id": "youtube.activities.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "publishedBefore": {"type": "string", "location": "query", "format": "date-time"}, "channelId": {"type": "string", "location": "query"}, "mine": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "5", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}, "pageToken": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "home": {"type": "string", "location": "query"}, "publishedAfter": {"type": "string", "location": "query", "format": "date-time"}}, "id": "youtube.activities.list", "httpMethod": "GET", "path": "activities", "response": {"$ref": "ActivityListResponse"}}}}', true));
    $this->search = new CFGS_Google_SearchServiceResource($this, $this->serviceName, 'search', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"topicId": {"type": "string", "location": "query"}, "onBehalfOfContentOwner": {"type": "string", "location": "query"}, "videoDimension": {"enum": ["2d", "3d", "any"], "type": "string", "location": "query"}, "videoLicense": {"enum": ["any", "creativeCommon", "youtube"], "type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "5", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}, "videoCaption": {"enum": ["any", "closedCaption", "none"], "type": "string", "location": "query"}, "videoDuration": {"enum": ["any", "long", "medium", "short"], "type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "q": {"type": "string", "location": "query"}, "published": {"enum": ["any", "thisWeek", "today"], "type": "string", "location": "query"}, "relatedToVideo": {"type": "string", "location": "query"}, "type": {"default": "video,channel,playlist", "type": "string", "location": "query"}, "order": {"default": "SEARCH_SORT_RELEVANCE", "enum": ["date", "rating", "relevance", "view_count"], "type": "string", "location": "query"}, "videoDefinition": {"enum": ["any", "high", "standard"], "type": "string", "location": "query"}}, "id": "youtube.search.list", "httpMethod": "GET", "path": "search", "response": {"$ref": "SearchListResponse"}}}}', true));
    $this->guideCategories = new CFGS_Google_GuideCategoriesServiceResource($this, $this->serviceName, 'guideCategories', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"regionCode": {"type": "string", "location": "query"}, "onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}, "hl": {"default": "en-US", "type": "string", "location": "query"}}, "id": "youtube.guideCategories.list", "httpMethod": "GET", "path": "guideCategories", "response": {"$ref": "GuideCategoryListResponse"}}}}', true));
    $this->playlists = new CFGS_Google_PlaylistsServiceResource($this, $this->serviceName, 'playlists', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "Playlist"}, "response": {"$ref": "Playlist"}, "httpMethod": "POST", "path": "playlists", "id": "youtube.playlists.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "mine": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "5", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}, "pageToken": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}}, "id": "youtube.playlists.list", "httpMethod": "GET", "path": "playlists", "response": {"$ref": "PlaylistListResponse"}}, "update": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "Playlist"}, "response": {"$ref": "Playlist"}, "httpMethod": "PUT", "path": "playlists", "id": "youtube.playlists.update"}, "delete": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "path": "playlists", "id": "youtube.playlists.delete", "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "httpMethod": "DELETE"}}}', true));
    $this->videos = new CFGS_Google_VideosServiceResource($this, $this->serviceName, 'videos', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.upload"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "supportsMediaUpload": true, "request": {"$ref": "Video"}, "mediaUpload": {"maxSize": "64GB", "protocols": {"simple": {"path": "/upload/youtube/v3/videos", "multipart": true}, "resumable": {"path": "/resumable/upload/youtube/v3/videos", "multipart": true}}, "accept": ["application/octet-stream", "video/*"]}, "response": {"$ref": "Video"}, "httpMethod": "POST", "path": "videos", "id": "youtube.videos.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "id": "youtube.videos.list", "httpMethod": "GET", "path": "videos", "response": {"$ref": "VideoListResponse"}}, "update": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "Video"}, "response": {"$ref": "Video"}, "httpMethod": "PUT", "path": "videos", "id": "youtube.videos.update"}, "delete": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "path": "videos", "id": "youtube.videos.delete", "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "httpMethod": "DELETE"}}}', true));
    $this->subscriptions = new CFGS_Google_SubscriptionsServiceResource($this, $this->serviceName, 'subscriptions', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "Subscription"}, "response": {"$ref": "Subscription"}, "httpMethod": "POST", "path": "subscriptions", "id": "youtube.subscriptions.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "channelId": {"type": "string", "location": "query"}, "mine": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "5", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}, "forChannelId": {"type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "order": {"default": "SUBSCRIPTION_ORDER_RELEVANCE", "enum": ["alphabetical", "relevance", "unread"], "type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}}, "id": "youtube.subscriptions.list", "httpMethod": "GET", "path": "subscriptions", "response": {"$ref": "SubscriptionListResponse"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "path": "subscriptions", "id": "youtube.subscriptions.delete", "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "httpMethod": "DELETE"}}}', true));
    $this->channels = new CFGS_Google_ChannelsServiceResource($this, $this->serviceName, 'channels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "mine": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "5", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}, "id": {"type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "mySubscribers": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "categoryId": {"type": "string", "location": "query"}}, "id": "youtube.channels.list", "httpMethod": "GET", "path": "channels", "response": {"$ref": "ChannelListResponse"}}}}', true));
    $this->playlistItems = new CFGS_Google_PlaylistItemsServiceResource($this, $this->serviceName, 'playlistItems', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "PlaylistItem"}, "response": {"$ref": "PlaylistItem"}, "httpMethod": "POST", "path": "playlistItems", "id": "youtube.playlistItems.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "playlistId": {"type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "5", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}, "pageToken": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}}, "id": "youtube.playlistItems.list", "httpMethod": "GET", "path": "playlistItems", "response": {"$ref": "PlaylistItemListResponse"}}, "update": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}}, "request": {"$ref": "PlaylistItem"}, "response": {"$ref": "PlaylistItem"}, "httpMethod": "PUT", "path": "playlistItems", "id": "youtube.playlistItems.update"}, "delete": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner"], "path": "playlistItems", "id": "youtube.playlistItems.delete", "parameters": {"onBehalfOfContentOwner": {"type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "httpMethod": "DELETE"}}}', true));
    $this->videoCategories = new CFGS_Google_VideoCategoriesServiceResource($this, $this->serviceName, 'videoCategories', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"], "parameters": {"regionCode": {"type": "string", "location": "query"}, "onBehalfOfContentOwner": {"type": "string", "location": "query"}, "part": {"required": true, "type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}, "hl": {"default": "en_US", "type": "string", "location": "query"}}, "id": "youtube.videoCategories.list", "httpMethod": "GET", "path": "videoCategories", "response": {"$ref": "VideoCategoryListResponse"}}}}', true));

  }
}

class CFGS_Google_Activity extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_ActivitySnippet';
  protected $__snippetDataType = '';
  public $snippet;
  protected $__contentDetailsType = 'CFGS_Google_ActivityContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(CFGS_Google_ActivitySnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setContentDetails(CFGS_Google_ActivityContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
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

class CFGS_Google_ActivityContentDetails extends CFGS_Google_Model {
  protected $__commentType = 'CFGS_Google_ActivityContentDetailsComment';
  protected $__commentDataType = '';
  public $comment;
  protected $__playlistItemType = 'CFGS_Google_ActivityContentDetailsPlaylistItem';
  protected $__playlistItemDataType = '';
  public $playlistItem;
  protected $__likeType = 'CFGS_Google_ActivityContentDetailsLike';
  protected $__likeDataType = '';
  public $like;
  protected $__socialType = 'CFGS_Google_ActivityContentDetailsSocial';
  protected $__socialDataType = '';
  public $social;
  protected $__favoriteType = 'CFGS_Google_ActivityContentDetailsFavorite';
  protected $__favoriteDataType = '';
  public $favorite;
  protected $__uploadType = 'CFGS_Google_ActivityContentDetailsUpload';
  protected $__uploadDataType = '';
  public $upload;
  protected $__recommendationType = 'CFGS_Google_ActivityContentDetailsRecommendation';
  protected $__recommendationDataType = '';
  public $recommendation;
  protected $__bulletinType = 'CFGS_Google_ActivityContentDetailsBulletin';
  protected $__bulletinDataType = '';
  public $bulletin;
  protected $__subscriptionType = 'CFGS_Google_ActivityContentDetailsSubscription';
  protected $__subscriptionDataType = '';
  public $subscription;
  public function setComment(CFGS_Google_ActivityContentDetailsComment $comment) {
    $this->comment = $comment;
  }
  public function getComment() {
    return $this->comment;
  }
  public function setPlaylistItem(CFGS_Google_ActivityContentDetailsPlaylistItem $playlistItem) {
    $this->playlistItem = $playlistItem;
  }
  public function getPlaylistItem() {
    return $this->playlistItem;
  }
  public function setLike(CFGS_Google_ActivityContentDetailsLike $like) {
    $this->like = $like;
  }
  public function getLike() {
    return $this->like;
  }
  public function setSocial(CFGS_Google_ActivityContentDetailsSocial $social) {
    $this->social = $social;
  }
  public function getSocial() {
    return $this->social;
  }
  public function setFavorite(CFGS_Google_ActivityContentDetailsFavorite $favorite) {
    $this->favorite = $favorite;
  }
  public function getFavorite() {
    return $this->favorite;
  }
  public function setUpload(CFGS_Google_ActivityContentDetailsUpload $upload) {
    $this->upload = $upload;
  }
  public function getUpload() {
    return $this->upload;
  }
  public function setRecommendation(CFGS_Google_ActivityContentDetailsRecommendation $recommendation) {
    $this->recommendation = $recommendation;
  }
  public function getRecommendation() {
    return $this->recommendation;
  }
  public function setBulletin(CFGS_Google_ActivityContentDetailsBulletin $bulletin) {
    $this->bulletin = $bulletin;
  }
  public function getBulletin() {
    return $this->bulletin;
  }
  public function setSubscription(CFGS_Google_ActivityContentDetailsSubscription $subscription) {
    $this->subscription = $subscription;
  }
  public function getSubscription() {
    return $this->subscription;
  }
}

class CFGS_Google_ActivityContentDetailsBulletin extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
}

class CFGS_Google_ActivityContentDetailsComment extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
}

class CFGS_Google_ActivityContentDetailsFavorite extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
}

class CFGS_Google_ActivityContentDetailsLike extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
}

class CFGS_Google_ActivityContentDetailsPlaylistItem extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public $playlistId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
  public function setPlaylistId($playlistId) {
    $this->playlistId = $playlistId;
  }
  public function getPlaylistId() {
    return $this->playlistId;
  }
}

class CFGS_Google_ActivityContentDetailsRecommendation extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public $reason;
  protected $__seedResourceIdType = 'CFGS_Google_ResourceId';
  protected $__seedResourceIdDataType = '';
  public $seedResourceId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
  public function setReason($reason) {
    $this->reason = $reason;
  }
  public function getReason() {
    return $this->reason;
  }
  public function setSeedResourceId(CFGS_Google_ResourceId $seedResourceId) {
    $this->seedResourceId = $seedResourceId;
  }
  public function getSeedResourceId() {
    return $this->seedResourceId;
  }
}

class CFGS_Google_ActivityContentDetailsSocial extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public $imageUrl;
  public $type;
  public $referenceUrl;
  public $author;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
  public function setImageUrl($imageUrl) {
    $this->imageUrl = $imageUrl;
  }
  public function getImageUrl() {
    return $this->imageUrl;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setReferenceUrl($referenceUrl) {
    $this->referenceUrl = $referenceUrl;
  }
  public function getReferenceUrl() {
    return $this->referenceUrl;
  }
  public function setAuthor($author) {
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
}

class CFGS_Google_ActivityContentDetailsSubscription extends CFGS_Google_Model {
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
}

class CFGS_Google_ActivityContentDetailsUpload extends CFGS_Google_Model {
  public $videoId;
  public function setVideoId($videoId) {
    $this->videoId = $videoId;
  }
  public function getVideoId() {
    return $this->videoId;
  }
}

class CFGS_Google_ActivityListResponse extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Activity';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $prevPageToken;
  protected $__pageInfoType = 'CFGS_Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
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
  public function setItems(/* array(CFGS_Google_Activity) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Activity', __METHOD__);
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
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setPageInfo(CFGS_Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class CFGS_Google_ActivitySnippet extends CFGS_Google_Model {
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public $title;
  public $channelId;
  public $publishedAt;
  public $type;
  public $groupId;
  public $description;
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setGroupId($groupId) {
    $this->groupId = $groupId;
  }
  public function getGroupId() {
    return $this->groupId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}

class CFGS_Google_Channel extends CFGS_Google_Model {
  protected $__topicDetailsType = 'CFGS_Google_ChannelTopicDetails';
  protected $__topicDetailsDataType = '';
  public $topicDetails;
  public $kind;
  protected $__statisticsType = 'CFGS_Google_ChannelStatistics';
  protected $__statisticsDataType = '';
  public $statistics;
  protected $__contentDetailsType = 'CFGS_Google_ChannelContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  protected $__snippetType = 'CFGS_Google_ChannelSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $etag;
  public $id;
  public function setTopicDetails(CFGS_Google_ChannelTopicDetails $topicDetails) {
    $this->topicDetails = $topicDetails;
  }
  public function getTopicDetails() {
    return $this->topicDetails;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStatistics(CFGS_Google_ChannelStatistics $statistics) {
    $this->statistics = $statistics;
  }
  public function getStatistics() {
    return $this->statistics;
  }
  public function setContentDetails(CFGS_Google_ChannelContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setSnippet(CFGS_Google_ChannelSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
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

class CFGS_Google_ChannelContentDetails extends CFGS_Google_Model {
  public $privacyStatus;
  public $uploads;
  public function setPrivacyStatus($privacyStatus) {
    $this->privacyStatus = $privacyStatus;
  }
  public function getPrivacyStatus() {
    return $this->privacyStatus;
  }
  public function setUploads($uploads) {
    $this->uploads = $uploads;
  }
  public function getUploads() {
    return $this->uploads;
  }
}

class CFGS_Google_ChannelListResponse extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Channel';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $prevPageToken;
  protected $__pageInfoType = 'CFGS_Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
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
  public function setItems(/* array(CFGS_Google_Channel) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Channel', __METHOD__);
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
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setPageInfo(CFGS_Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class CFGS_Google_ChannelSnippet extends CFGS_Google_Model {
  public $title;
  public $channelId;
  public $description;
  public $publishedAt;
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
}

class CFGS_Google_ChannelStatistics extends CFGS_Google_Model {
  public $commentCount;
  public $subscriberCount;
  public $videoCount;
  public $viewCount;
  public function setCommentCount($commentCount) {
    $this->commentCount = $commentCount;
  }
  public function getCommentCount() {
    return $this->commentCount;
  }
  public function setSubscriberCount($subscriberCount) {
    $this->subscriberCount = $subscriberCount;
  }
  public function getSubscriberCount() {
    return $this->subscriberCount;
  }
  public function setVideoCount($videoCount) {
    $this->videoCount = $videoCount;
  }
  public function getVideoCount() {
    return $this->videoCount;
  }
  public function setViewCount($viewCount) {
    $this->viewCount = $viewCount;
  }
  public function getViewCount() {
    return $this->viewCount;
  }
}

class CFGS_Google_ChannelTopicDetails extends CFGS_Google_Model {
  public $topicIds;
  public function setTopicIds($topicIds) {
    $this->topicIds = $topicIds;
  }
  public function getTopicIds() {
    return $this->topicIds;
  }
}

class CFGS_Google_GuideCategory extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_GuideCategorySnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(CFGS_Google_GuideCategorySnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
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

class CFGS_Google_GuideCategoryListResponse extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_GuideCategory';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setItems(/* array(CFGS_Google_GuideCategory) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_GuideCategory', __METHOD__);
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
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class CFGS_Google_GuideCategorySnippet extends CFGS_Google_Model {
  public $channelId;
  public $title;
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
}

class CFGS_Google_PageInfo extends CFGS_Google_Model {
  public $totalResults;
  public $resultsPerPage;
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setResultsPerPage($resultsPerPage) {
    $this->resultsPerPage = $resultsPerPage;
  }
  public function getResultsPerPage() {
    return $this->resultsPerPage;
  }
}

class CFGS_Google_Playlist extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_PlaylistSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  protected $__statusType = 'CFGS_Google_PlaylistStatus';
  protected $__statusDataType = '';
  public $status;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(CFGS_Google_PlaylistSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setStatus(CFGS_Google_PlaylistStatus $status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
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

class CFGS_Google_PlaylistItem extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_PlaylistItemSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  protected $__contentDetailsType = 'CFGS_Google_PlaylistItemContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(CFGS_Google_PlaylistItemSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setContentDetails(CFGS_Google_PlaylistItemContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
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

class CFGS_Google_PlaylistItemContentDetails extends CFGS_Google_Model {
  public $note;
  public $startAt;
  public $endAt;
  public $videoId;
  public function setNote($note) {
    $this->note = $note;
  }
  public function getNote() {
    return $this->note;
  }
  public function setStartAt($startAt) {
    $this->startAt = $startAt;
  }
  public function getStartAt() {
    return $this->startAt;
  }
  public function setEndAt($endAt) {
    $this->endAt = $endAt;
  }
  public function getEndAt() {
    return $this->endAt;
  }
  public function setVideoId($videoId) {
    $this->videoId = $videoId;
  }
  public function getVideoId() {
    return $this->videoId;
  }
}

class CFGS_Google_PlaylistItemListResponse extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_PlaylistItem';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $prevPageToken;
  protected $__pageInfoType = 'CFGS_Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
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
  public function setItems(/* array(CFGS_Google_PlaylistItem) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_PlaylistItem', __METHOD__);
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
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setPageInfo(CFGS_Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class CFGS_Google_PlaylistItemSnippet extends CFGS_Google_Model {
  public $playlistId;
  public $description;
  public $title;
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public $channelId;
  public $publishedAt;
  public $position;
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public function setPlaylistId($playlistId) {
    $this->playlistId = $playlistId;
  }
  public function getPlaylistId() {
    return $this->playlistId;
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
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setPosition($position) {
    $this->position = $position;
  }
  public function getPosition() {
    return $this->position;
  }
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
}

class CFGS_Google_PlaylistListResponse extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Playlist';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $prevPageToken;
  protected $__pageInfoType = 'CFGS_Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
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
  public function setItems(/* array(CFGS_Google_Playlist) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Playlist', __METHOD__);
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
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setPageInfo(CFGS_Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class CFGS_Google_PlaylistSnippet extends CFGS_Google_Model {
  public $title;
  public $channelId;
  public $description;
  public $publishedAt;
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
}

class CFGS_Google_PlaylistStatus extends CFGS_Google_Model {
  public $privacyStatus;
  public function setPrivacyStatus($privacyStatus) {
    $this->privacyStatus = $privacyStatus;
  }
  public function getPrivacyStatus() {
    return $this->privacyStatus;
  }
}

class CFGS_Google_ResourceId extends CFGS_Google_Model {
  public $kind;
  public $channelId;
  public $playlistId;
  public $videoId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPlaylistId($playlistId) {
    $this->playlistId = $playlistId;
  }
  public function getPlaylistId() {
    return $this->playlistId;
  }
  public function setVideoId($videoId) {
    $this->videoId = $videoId;
  }
  public function getVideoId() {
    return $this->videoId;
  }
}

class CFGS_Google_SearchListResponse extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_SearchResult';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $prevPageToken;
  protected $__pageInfoType = 'CFGS_Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
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
  public function setItems(/* array(CFGS_Google_SearchResult) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_SearchResult', __METHOD__);
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
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setPageInfo(CFGS_Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class CFGS_Google_SearchResult extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_SearchResultSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $kind;
  public $etag;
  protected $__idType = 'CFGS_Google_ResourceId';
  protected $__idDataType = '';
  public $id;
  public function setSnippet(CFGS_Google_SearchResultSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId(CFGS_Google_ResourceId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_SearchResultSnippet extends CFGS_Google_Model {
  public $title;
  public $channelId;
  public $description;
  public $publishedAt;
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
}

class CFGS_Google_Subscription extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_SubscriptionSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  protected $__contentDetailsType = 'CFGS_Google_SubscriptionContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(CFGS_Google_SubscriptionSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setContentDetails(CFGS_Google_SubscriptionContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
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

class CFGS_Google_SubscriptionContentDetails extends CFGS_Google_Model {
  public $newItemCount;
  public $totalItemCount;
  public function setNewItemCount($newItemCount) {
    $this->newItemCount = $newItemCount;
  }
  public function getNewItemCount() {
    return $this->newItemCount;
  }
  public function setTotalItemCount($totalItemCount) {
    $this->totalItemCount = $totalItemCount;
  }
  public function getTotalItemCount() {
    return $this->totalItemCount;
  }
}

class CFGS_Google_SubscriptionListResponse extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Subscription';
  protected $__itemsDataType = 'array';
  public $items;
  public $etag;
  public $prevPageToken;
  protected $__pageInfoType = 'CFGS_Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
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
  public function setItems(/* array(CFGS_Google_Subscription) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Subscription', __METHOD__);
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
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setPageInfo(CFGS_Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class CFGS_Google_SubscriptionSnippet extends CFGS_Google_Model {
  public $description;
  public $title;
  protected $__resourceIdType = 'CFGS_Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public $channelId;
  public $publishedAt;
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
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
  public function setResourceId(CFGS_Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
}

class CFGS_Google_Thumbnail extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_Video extends CFGS_Google_Model {
  protected $__statusType = 'CFGS_Google_VideoStatus';
  protected $__statusDataType = '';
  public $status;
  protected $__topicDetailsType = 'CFGS_Google_VideoTopicDetails';
  protected $__topicDetailsDataType = '';
  public $topicDetails;
  public $kind;
  protected $__statisticsType = 'CFGS_Google_VideoStatistics';
  protected $__statisticsDataType = '';
  public $statistics;
  protected $__contentDetailsType = 'CFGS_Google_VideoContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  protected $__snippetType = 'CFGS_Google_VideoSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  protected $__playerType = 'CFGS_Google_VideoPlayer';
  protected $__playerDataType = '';
  public $player;
  public $etag;
  public $id;
  public function setStatus(CFGS_Google_VideoStatus $status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setTopicDetails(CFGS_Google_VideoTopicDetails $topicDetails) {
    $this->topicDetails = $topicDetails;
  }
  public function getTopicDetails() {
    return $this->topicDetails;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStatistics(CFGS_Google_VideoStatistics $statistics) {
    $this->statistics = $statistics;
  }
  public function getStatistics() {
    return $this->statistics;
  }
  public function setContentDetails(CFGS_Google_VideoContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setSnippet(CFGS_Google_VideoSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setPlayer(CFGS_Google_VideoPlayer $player) {
    $this->player = $player;
  }
  public function getPlayer() {
    return $this->player;
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

class CFGS_Google_VideoCategory extends CFGS_Google_Model {
  protected $__snippetType = 'CFGS_Google_VideoCategorySnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(CFGS_Google_VideoCategorySnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
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

class CFGS_Google_VideoCategoryListResponse extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_VideoCategory';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setItems(/* array(CFGS_Google_VideoCategory) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_VideoCategory', __METHOD__);
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
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class CFGS_Google_VideoCategorySnippet extends CFGS_Google_Model {
  public $channelId;
  public $title;
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
}

class CFGS_Google_VideoContentDetails extends CFGS_Google_Model {
  public $duration;
  protected $__regionRestrictionType = 'CFGS_Google_VideoContentDetailsRegionRestriction';
  protected $__regionRestrictionDataType = '';
  public $regionRestriction;
  public function setDuration($duration) {
    $this->duration = $duration;
  }
  public function getDuration() {
    return $this->duration;
  }
  public function setRegionRestriction(CFGS_Google_VideoContentDetailsRegionRestriction $regionRestriction) {
    $this->regionRestriction = $regionRestriction;
  }
  public function getRegionRestriction() {
    return $this->regionRestriction;
  }
}

class CFGS_Google_VideoContentDetailsRegionRestriction extends CFGS_Google_Model {
  public $blocked;
  public $allowed;
  public function setBlocked($blocked) {
    $this->blocked = $blocked;
  }
  public function getBlocked() {
    return $this->blocked;
  }
  public function setAllowed($allowed) {
    $this->allowed = $allowed;
  }
  public function getAllowed() {
    return $this->allowed;
  }
}

class CFGS_Google_VideoListResponse extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_Video';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setItems(/* array(CFGS_Google_Video) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Video', __METHOD__);
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
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class CFGS_Google_VideoPlayer extends CFGS_Google_Model {
  public $embedHtml;
  public function setEmbedHtml($embedHtml) {
    $this->embedHtml = $embedHtml;
  }
  public function getEmbedHtml() {
    return $this->embedHtml;
  }
}

class CFGS_Google_VideoSnippet extends CFGS_Google_Model {
  protected $__thumbnailsType = 'CFGS_Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public $tags;
  public $channelId;
  public $publishedAt;
  public $title;
  public $categoryId;
  public $description;
  public function setThumbnails(CFGS_Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
  public function setTags($tags) {
    $this->tags = $tags;
  }
  public function getTags() {
    return $this->tags;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }
  public function getCategoryId() {
    return $this->categoryId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}

class CFGS_Google_VideoStatistics extends CFGS_Google_Model {
  public $commentCount;
  public $viewCount;
  public $favoriteCount;
  public $dislikeCount;
  public $likeCount;
  public function setCommentCount($commentCount) {
    $this->commentCount = $commentCount;
  }
  public function getCommentCount() {
    return $this->commentCount;
  }
  public function setViewCount($viewCount) {
    $this->viewCount = $viewCount;
  }
  public function getViewCount() {
    return $this->viewCount;
  }
  public function setFavoriteCount($favoriteCount) {
    $this->favoriteCount = $favoriteCount;
  }
  public function getFavoriteCount() {
    return $this->favoriteCount;
  }
  public function setDislikeCount($dislikeCount) {
    $this->dislikeCount = $dislikeCount;
  }
  public function getDislikeCount() {
    return $this->dislikeCount;
  }
  public function setLikeCount($likeCount) {
    $this->likeCount = $likeCount;
  }
  public function getLikeCount() {
    return $this->likeCount;
  }
}

class CFGS_Google_VideoStatus extends CFGS_Google_Model {
  public $privacyStatus;
  public $uploadStatus;
  public $rejectionReason;
  public $failureReason;
  public function setPrivacyStatus($privacyStatus) {
    $this->privacyStatus = $privacyStatus;
  }
  public function getPrivacyStatus() {
    return $this->privacyStatus;
  }
  public function setUploadStatus($uploadStatus) {
    $this->uploadStatus = $uploadStatus;
  }
  public function getUploadStatus() {
    return $this->uploadStatus;
  }
  public function setRejectionReason($rejectionReason) {
    $this->rejectionReason = $rejectionReason;
  }
  public function getRejectionReason() {
    return $this->rejectionReason;
  }
  public function setFailureReason($failureReason) {
    $this->failureReason = $failureReason;
  }
  public function getFailureReason() {
    return $this->failureReason;
  }
}

class CFGS_Google_VideoTopicDetails extends CFGS_Google_Model {
  public $topicIds;
  public function setTopicIds($topicIds) {
    $this->topicIds = $topicIds;
  }
  public function getTopicIds() {
    return $this->topicIds;
  }
}
