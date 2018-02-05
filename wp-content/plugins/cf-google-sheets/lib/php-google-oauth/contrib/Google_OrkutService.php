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
   * The "communityMembers" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityMembers = $orkutService->communityMembers;
   *  </code>
   */
  class CFGS_Google_CommunityMembersServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Makes the user join a community. (communityMembers.insert)
     *
     * @param int $communityId ID of the community.
     * @param string $userId ID of the user.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_CommunityMembers
     */
    public function insert($communityId, $userId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityMembers($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the relationship between a user and a community. (communityMembers.get)
     *
     * @param int $communityId ID of the community.
     * @param string $userId ID of the user.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityMembers
     */
    public function get($communityId, $userId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityMembers($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists members of a community. Use the pagination tokens to retrieve the full list; do not rely on
     * the member count available in the community profile information to know when to stop iterating,
     * as that count may be approximate. (communityMembers.list)
     *
     * @param int $communityId The ID of the community whose members will be listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param bool friendsOnly Whether to list only community members who are friends of the user.
     * @opt_param string maxResults The maximum number of members to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityMembersList
     */
    public function listCommunityMembers($communityId, $optParams = array()) {
      $params = array('communityId' => $communityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityMembersList($data);
      } else {
        return $data;
      }
    }
    /**
     * Makes the user leave a community. (communityMembers.delete)
     *
     * @param int $communityId ID of the community.
     * @param string $userId ID of the user.
     * @param array $optParams Optional parameters.
     */
    public function delete($communityId, $userId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "activities" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $activities = $orkutService->activities;
   *  </code>
   */
  class CFGS_Google_ActivitiesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Retrieves a list of activities. (activities.list)
     *
     * @param string $userId The ID of the user whose activities will be listed. Can be me to refer to the viewer (i.e. the authenticated user).
     * @param string $collection The collection of activities to list.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param string maxResults The maximum number of activities to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_ActivityList
     */
    public function listActivities($userId, $collection, $optParams = array()) {
      $params = array('userId' => $userId, 'collection' => $collection);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_ActivityList($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes an existing activity, if the access controls allow it. (activities.delete)
     *
     * @param string $activityId ID of the activity to remove.
     * @param array $optParams Optional parameters.
     */
    public function delete($activityId, $optParams = array()) {
      $params = array('activityId' => $activityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "communityPollComments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityPollComments = $orkutService->communityPollComments;
   *  </code>
   */
  class CFGS_Google_CommunityPollCommentsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Adds a comment on a community poll. (communityPollComments.insert)
     *
     * @param int $communityId The ID of the community whose poll is being commented.
     * @param string $pollId The ID of the poll being commented.
     * @param CFGS_Google_CommunityPollComment $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_CommunityPollComment
     */
    public function insert($communityId, $pollId, CFGS_Google_CommunityPollComment $postBody, $optParams = array()) {
      $params = array('communityId' => $communityId, 'pollId' => $pollId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityPollComment($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the comments of a community poll. (communityPollComments.list)
     *
     * @param int $communityId The ID of the community whose poll is having its comments listed.
     * @param string $pollId The ID of the community whose polls will be listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param string maxResults The maximum number of comments to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityPollCommentList
     */
    public function listCommunityPollComments($communityId, $pollId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'pollId' => $pollId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityPollCommentList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "communityPolls" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityPolls = $orkutService->communityPolls;
   *  </code>
   */
  class CFGS_Google_CommunityPollsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Retrieves the polls of a community. (communityPolls.list)
     *
     * @param int $communityId The ID of the community which polls will be listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param string maxResults The maximum number of polls to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityPollList
     */
    public function listCommunityPolls($communityId, $optParams = array()) {
      $params = array('communityId' => $communityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityPollList($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves one specific poll of a community. (communityPolls.get)
     *
     * @param int $communityId The ID of the community for whose poll will be retrieved.
     * @param string $pollId The ID of the poll to get.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityPoll
     */
    public function get($communityId, $pollId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'pollId' => $pollId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityPoll($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "communityMessages" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityMessages = $orkutService->communityMessages;
   *  </code>
   */
  class CFGS_Google_CommunityMessagesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Adds a message to a given community topic. (communityMessages.insert)
     *
     * @param int $communityId The ID of the community the message should be added to.
     * @param string $topicId The ID of the topic the message should be added to.
     * @param CFGS_Google_CommunityMessage $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_CommunityMessage
     */
    public function insert($communityId, $topicId, CFGS_Google_CommunityMessage $postBody, $optParams = array()) {
      $params = array('communityId' => $communityId, 'topicId' => $topicId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityMessage($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the messages of a topic of a community. (communityMessages.list)
     *
     * @param int $communityId The ID of the community which messages will be listed.
     * @param string $topicId The ID of the topic which messages will be listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param string maxResults The maximum number of messages to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityMessageList
     */
    public function listCommunityMessages($communityId, $topicId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'topicId' => $topicId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityMessageList($data);
      } else {
        return $data;
      }
    }
    /**
     * Moves a message of the community to the trash folder. (communityMessages.delete)
     *
     * @param int $communityId The ID of the community whose message will be moved to the trash folder.
     * @param string $topicId The ID of the topic whose message will be moved to the trash folder.
     * @param string $messageId The ID of the message to be moved to the trash folder.
     * @param array $optParams Optional parameters.
     */
    public function delete($communityId, $topicId, $messageId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'topicId' => $topicId, 'messageId' => $messageId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "communityTopics" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityTopics = $orkutService->communityTopics;
   *  </code>
   */
  class CFGS_Google_CommunityTopicsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Adds a topic to a given community. (communityTopics.insert)
     *
     * @param int $communityId The ID of the community the topic should be added to.
     * @param CFGS_Google_CommunityTopic $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool isShout Whether this topic is a shout.
     * @return CFGS_Google_CommunityTopic
     */
    public function insert($communityId, CFGS_Google_CommunityTopic $postBody, $optParams = array()) {
      $params = array('communityId' => $communityId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityTopic($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a topic of a community. (communityTopics.get)
     *
     * @param int $communityId The ID of the community whose topic will be retrieved.
     * @param string $topicId The ID of the topic to get.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityTopic
     */
    public function get($communityId, $topicId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'topicId' => $topicId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityTopic($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the topics of a community. (communityTopics.list)
     *
     * @param int $communityId The ID of the community which topics will be listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param string maxResults The maximum number of topics to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityTopicList
     */
    public function listCommunityTopics($communityId, $optParams = array()) {
      $params = array('communityId' => $communityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityTopicList($data);
      } else {
        return $data;
      }
    }
    /**
     * Moves a topic of the community to the trash folder. (communityTopics.delete)
     *
     * @param int $communityId The ID of the community whose topic will be moved to the trash folder.
     * @param string $topicId The ID of the topic to be moved to the trash folder.
     * @param array $optParams Optional parameters.
     */
    public function delete($communityId, $topicId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'topicId' => $topicId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "comments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $comments = $orkutService->comments;
   *  </code>
   */
  class CFGS_Google_CommentsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Inserts a new comment to an activity. (comments.insert)
     *
     * @param string $activityId The ID of the activity to contain the new comment.
     * @param CFGS_Google_Comment $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Comment
     */
    public function insert($activityId, CFGS_Google_Comment $postBody, $optParams = array()) {
      $params = array('activityId' => $activityId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Comment($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves an existing comment. (comments.get)
     *
     * @param string $commentId ID of the comment to get.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
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
    /**
     * Retrieves a list of comments, possibly filtered. (comments.list)
     *
     * @param string $activityId The ID of the activity containing the comments.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string orderBy Sort search results.
     * @opt_param string pageToken A continuation token that allows pagination.
     * @opt_param string maxResults The maximum number of activities to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommentList
     */
    public function listComments($activityId, $optParams = array()) {
      $params = array('activityId' => $activityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommentList($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes an existing comment. (comments.delete)
     *
     * @param string $commentId ID of the comment to remove.
     * @param array $optParams Optional parameters.
     */
    public function delete($commentId, $optParams = array()) {
      $params = array('commentId' => $commentId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "acl" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $acl = $orkutService->acl;
   *  </code>
   */
  class CFGS_Google_AclServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Excludes an element from the ACL of the activity. (acl.delete)
     *
     * @param string $activityId ID of the activity.
     * @param string $userId ID of the user to be removed from the activity.
     * @param array $optParams Optional parameters.
     */
    public function delete($activityId, $userId, $optParams = array()) {
      $params = array('activityId' => $activityId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "communityRelated" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityRelated = $orkutService->communityRelated;
   *  </code>
   */
  class CFGS_Google_CommunityRelatedServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Retrieves the communities related to another one. (communityRelated.list)
     *
     * @param int $communityId The ID of the community whose related communities will be listed.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityList
     */
    public function listCommunityRelated($communityId, $optParams = array()) {
      $params = array('communityId' => $communityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "scraps" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $scraps = $orkutService->scraps;
   *  </code>
   */
  class CFGS_Google_ScrapsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Creates a new scrap. (scraps.insert)
     *
     * @param CFGS_Google_Activity $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Activity
     */
    public function insert(CFGS_Google_Activity $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Activity($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "communityPollVotes" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityPollVotes = $orkutService->communityPollVotes;
   *  </code>
   */
  class CFGS_Google_CommunityPollVotesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Votes on a community poll. (communityPollVotes.insert)
     *
     * @param int $communityId The ID of the community whose poll is being voted.
     * @param string $pollId The ID of the poll being voted.
     * @param CFGS_Google_CommunityPollVote $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_CommunityPollVote
     */
    public function insert($communityId, $pollId, CFGS_Google_CommunityPollVote $postBody, $optParams = array()) {
      $params = array('communityId' => $communityId, 'pollId' => $pollId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityPollVote($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "communities" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communities = $orkutService->communities;
   *  </code>
   */
  class CFGS_Google_CommunitiesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Retrieves the list of communities the current user is a member of. (communities.list)
     *
     * @param string $userId The ID of the user whose communities will be listed. Can be me to refer to caller.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string orderBy How to order the communities by.
     * @opt_param string maxResults The maximum number of communities to include in the response.
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_CommunityList
     */
    public function listCommunities($userId, $optParams = array()) {
      $params = array('userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityList($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves the basic information (aka. profile) of a community. (communities.get)
     *
     * @param int $communityId The ID of the community to get.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string hl Specifies the interface language (host language) of your user interface.
     * @return CFGS_Google_Community
     */
    public function get($communityId, $optParams = array()) {
      $params = array('communityId' => $communityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Community($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "communityFollow" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $communityFollow = $orkutService->communityFollow;
   *  </code>
   */
  class CFGS_Google_CommunityFollowServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Adds a user as a follower of a community. (communityFollow.insert)
     *
     * @param int $communityId ID of the community.
     * @param string $userId ID of the user.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_CommunityMembers
     */
    public function insert($communityId, $userId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CommunityMembers($data);
      } else {
        return $data;
      }
    }
    /**
     * Removes a user from the followers of a community. (communityFollow.delete)
     *
     * @param int $communityId ID of the community.
     * @param string $userId ID of the user.
     * @param array $optParams Optional parameters.
     */
    public function delete($communityId, $userId, $optParams = array()) {
      $params = array('communityId' => $communityId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "activityVisibility" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $activityVisibility = $orkutService->activityVisibility;
   *  </code>
   */
  class CFGS_Google_ActivityVisibilityServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Updates the visibility of an existing activity. This method supports patch semantics.
     * (activityVisibility.patch)
     *
     * @param string $activityId ID of the activity.
     * @param CFGS_Google_Visibility $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Visibility
     */
    public function patch($activityId, CFGS_Google_Visibility $postBody, $optParams = array()) {
      $params = array('activityId' => $activityId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Visibility($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the visibility of an existing activity. (activityVisibility.update)
     *
     * @param string $activityId ID of the activity.
     * @param CFGS_Google_Visibility $postBody
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Visibility
     */
    public function update($activityId, CFGS_Google_Visibility $postBody, $optParams = array()) {
      $params = array('activityId' => $activityId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Visibility($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets the visibility of an existing activity. (activityVisibility.get)
     *
     * @param string $activityId ID of the activity to get the visibility.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Visibility
     */
    public function get($activityId, $optParams = array()) {
      $params = array('activityId' => $activityId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Visibility($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "badges" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $badges = $orkutService->badges;
   *  </code>
   */
  class CFGS_Google_BadgesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Retrieves the list of visible badges of a user. (badges.list)
     *
     * @param string $userId The id of the user whose badges will be listed. Can be me to refer to caller.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_BadgeList
     */
    public function listBadges($userId, $optParams = array()) {
      $params = array('userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_BadgeList($data);
      } else {
        return $data;
      }
    }
    /**
     * Retrieves a badge from a user. (badges.get)
     *
     * @param string $userId The ID of the user whose badges will be listed. Can be me to refer to caller.
     * @param string $badgeId The ID of the badge that will be retrieved.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Badge
     */
    public function get($userId, $badgeId, $optParams = array()) {
      $params = array('userId' => $userId, 'badgeId' => $badgeId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Badge($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "counters" collection of methods.
   * Typical usage is:
   *  <code>
   *   $orkutService = new CFGS_Google_OrkutService(...);
   *   $counters = $orkutService->counters;
   *  </code>
   */
  class CFGS_Google_CountersServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Retrieves the counters of a user. (counters.list)
     *
     * @param string $userId The ID of the user whose counters will be listed. Can be me to refer to caller.
     * @param array $optParams Optional parameters.
     * @return CFGS_Google_Counters
     */
    public function listCounters($userId, $optParams = array()) {
      $params = array('userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Counters($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for CFGS_Google_Orkut (v2).
 *
 * <p>
 * Lets you manage activities, comments and badges in Orkut. More stuff coming in time.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/orkut/v2/reference.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class CFGS_Google_OrkutService extends CFGS_Google_Service {
  public $communityMembers;
  public $activities;
  public $communityPollComments;
  public $communityPolls;
  public $communityMessages;
  public $communityTopics;
  public $comments;
  public $acl;
  public $communityRelated;
  public $scraps;
  public $communityPollVotes;
  public $communities;
  public $communityFollow;
  public $activityVisibility;
  public $badges;
  public $counters;
  /**
   * Constructs the internal representation of the Orkut service.
   *
   * @param CFGS_Google_Client $client
   */
  public function __construct(CFGS_Google_Client $client) {
    $this->servicePath = 'orkut/v2/';
    $this->version = 'v2';
    $this->serviceName = 'orkut';

    $client->addService($this->serviceName, $this->version);
    $this->communityMembers = new CFGS_Google_CommunityMembersServiceResource($this, $this->serviceName, 'communityMembers', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "userId": {"required": true, "type": "string", "location": "path"}}, "id": "orkut.communityMembers.insert", "httpMethod": "POST", "path": "communities/{communityId}/members/{userId}", "response": {"$ref": "CommunityMembers"}}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "userId": {"required": true, "type": "string", "location": "path"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityMembers.get", "httpMethod": "GET", "path": "communities/{communityId}/members/{userId}", "response": {"$ref": "CommunityMembers"}}, "list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "friendsOnly": {"type": "boolean", "location": "query"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "uint32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityMembers.list", "httpMethod": "GET", "path": "communities/{communityId}/members", "response": {"$ref": "CommunityMembersList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "communities/{communityId}/members/{userId}", "id": "orkut.communityMembers.delete", "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "userId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->activities = new CFGS_Google_ActivitiesServiceResource($this, $this->serviceName, 'activities', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "1", "type": "integer", "maximum": "100", "format": "uint32"}, "userId": {"required": true, "type": "string", "location": "path"}, "hl": {"type": "string", "location": "query"}, "collection": {"required": true, "type": "string", "location": "path", "enum": ["all", "scraps", "stream"]}}, "id": "orkut.activities.list", "httpMethod": "GET", "path": "people/{userId}/activities/{collection}", "response": {"$ref": "ActivityList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "activities/{activityId}", "id": "orkut.activities.delete", "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->communityPollComments = new CFGS_Google_CommunityPollCommentsServiceResource($this, $this->serviceName, 'communityPollComments', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "pollId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CommunityPollComment"}, "response": {"$ref": "CommunityPollComment"}, "httpMethod": "POST", "path": "communities/{communityId}/polls/{pollId}/comments", "id": "orkut.communityPollComments.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"pollId": {"required": true, "type": "string", "location": "path"}, "pageToken": {"type": "string", "location": "query"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "uint32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityPollComments.list", "httpMethod": "GET", "path": "communities/{communityId}/polls/{pollId}/comments", "response": {"$ref": "CommunityPollCommentList"}}}}', true));
    $this->communityPolls = new CFGS_Google_CommunityPollsServiceResource($this, $this->serviceName, 'communityPolls', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "uint32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityPolls.list", "httpMethod": "GET", "path": "communities/{communityId}/polls", "response": {"$ref": "CommunityPollList"}}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "pollId": {"required": true, "type": "string", "location": "path"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityPolls.get", "httpMethod": "GET", "path": "communities/{communityId}/polls/{pollId}", "response": {"$ref": "CommunityPoll"}}}}', true));
    $this->communityMessages = new CFGS_Google_CommunityMessagesServiceResource($this, $this->serviceName, 'communityMessages', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"topicId": {"required": true, "type": "string", "location": "path", "format": "int64"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}}, "request": {"$ref": "CommunityMessage"}, "response": {"$ref": "CommunityMessage"}, "httpMethod": "POST", "path": "communities/{communityId}/topics/{topicId}/messages", "id": "orkut.communityMessages.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "maxResults": {"location": "query", "minimum": "1", "type": "integer", "maximum": "100", "format": "uint32"}, "hl": {"type": "string", "location": "query"}, "topicId": {"required": true, "type": "string", "location": "path", "format": "int64"}}, "id": "orkut.communityMessages.list", "httpMethod": "GET", "path": "communities/{communityId}/topics/{topicId}/messages", "response": {"$ref": "CommunityMessageList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "communities/{communityId}/topics/{topicId}/messages/{messageId}", "id": "orkut.communityMessages.delete", "parameters": {"topicId": {"required": true, "type": "string", "location": "path", "format": "int64"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "messageId": {"required": true, "type": "string", "location": "path", "format": "int64"}}, "httpMethod": "DELETE"}}}', true));
    $this->communityTopics = new CFGS_Google_CommunityTopicsServiceResource($this, $this->serviceName, 'communityTopics', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"isShout": {"type": "boolean", "location": "query"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}}, "request": {"$ref": "CommunityTopic"}, "response": {"$ref": "CommunityTopic"}, "httpMethod": "POST", "path": "communities/{communityId}/topics", "id": "orkut.communityTopics.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"topicId": {"required": true, "type": "string", "location": "path", "format": "int64"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityTopics.get", "httpMethod": "GET", "path": "communities/{communityId}/topics/{topicId}", "response": {"$ref": "CommunityTopic"}}, "list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "maxResults": {"location": "query", "minimum": "1", "type": "integer", "maximum": "100", "format": "uint32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityTopics.list", "httpMethod": "GET", "path": "communities/{communityId}/topics", "response": {"$ref": "CommunityTopicList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "communities/{communityId}/topics/{topicId}", "id": "orkut.communityTopics.delete", "parameters": {"topicId": {"required": true, "type": "string", "location": "path", "format": "int64"}, "communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}}, "httpMethod": "DELETE"}}}', true));
    $this->comments = new CFGS_Google_CommentsServiceResource($this, $this->serviceName, 'comments', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Comment"}, "response": {"$ref": "Comment"}, "httpMethod": "POST", "path": "activities/{activityId}/comments", "id": "orkut.comments.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"commentId": {"required": true, "type": "string", "location": "path"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.comments.get", "httpMethod": "GET", "path": "comments/{commentId}", "response": {"$ref": "Comment"}}, "list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"orderBy": {"default": "DESCENDING_SORT", "enum": ["ascending", "descending"], "type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "activityId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "uint32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.comments.list", "httpMethod": "GET", "path": "activities/{activityId}/comments", "response": {"$ref": "CommentList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "comments/{commentId}", "id": "orkut.comments.delete", "parameters": {"commentId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->acl = new CFGS_Google_AclServiceResource($this, $this->serviceName, 'acl', json_decode('{"methods": {"delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "activities/{activityId}/acl/{userId}", "id": "orkut.acl.delete", "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}, "userId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->communityRelated = new CFGS_Google_CommunityRelatedServiceResource($this, $this->serviceName, 'communityRelated', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communityRelated.list", "httpMethod": "GET", "path": "communities/{communityId}/related", "response": {"$ref": "CommunityList"}}}}', true));
    $this->scraps = new CFGS_Google_ScrapsServiceResource($this, $this->serviceName, 'scraps', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "request": {"$ref": "Activity"}, "response": {"$ref": "Activity"}, "httpMethod": "POST", "path": "activities/scraps", "id": "orkut.scraps.insert"}}}', true));
    $this->communityPollVotes = new CFGS_Google_CommunityPollVotesServiceResource($this, $this->serviceName, 'communityPollVotes', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "pollId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CommunityPollVote"}, "response": {"$ref": "CommunityPollVote"}, "httpMethod": "POST", "path": "communities/{communityId}/polls/{pollId}/votes", "id": "orkut.communityPollVotes.insert"}}}', true));
    $this->communities = new CFGS_Google_CommunitiesServiceResource($this, $this->serviceName, 'communities', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"orderBy": {"enum": ["id", "ranked"], "type": "string", "location": "query"}, "userId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "uint32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communities.list", "httpMethod": "GET", "path": "people/{userId}/communities", "response": {"$ref": "CommunityList"}}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "hl": {"type": "string", "location": "query"}}, "id": "orkut.communities.get", "httpMethod": "GET", "path": "communities/{communityId}", "response": {"$ref": "Community"}}}}', true));
    $this->communityFollow = new CFGS_Google_CommunityFollowServiceResource($this, $this->serviceName, 'communityFollow', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "userId": {"required": true, "type": "string", "location": "path"}}, "id": "orkut.communityFollow.insert", "httpMethod": "POST", "path": "communities/{communityId}/followers/{userId}", "response": {"$ref": "CommunityMembers"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/orkut"], "path": "communities/{communityId}/followers/{userId}", "id": "orkut.communityFollow.delete", "parameters": {"communityId": {"required": true, "type": "integer", "location": "path", "format": "int32"}, "userId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->activityVisibility = new CFGS_Google_ActivityVisibilityServiceResource($this, $this->serviceName, 'activityVisibility', json_decode('{"methods": {"patch": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Visibility"}, "response": {"$ref": "Visibility"}, "httpMethod": "PATCH", "path": "activities/{activityId}/visibility", "id": "orkut.activityVisibility.patch"}, "update": {"scopes": ["https://www.googleapis.com/auth/orkut"], "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Visibility"}, "response": {"$ref": "Visibility"}, "httpMethod": "PUT", "path": "activities/{activityId}/visibility", "id": "orkut.activityVisibility.update"}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"activityId": {"required": true, "type": "string", "location": "path"}}, "id": "orkut.activityVisibility.get", "httpMethod": "GET", "path": "activities/{activityId}/visibility", "response": {"$ref": "Visibility"}}}}', true));
    $this->badges = new CFGS_Google_BadgesServiceResource($this, $this->serviceName, 'badges', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"userId": {"required": true, "type": "string", "location": "path"}}, "id": "orkut.badges.list", "httpMethod": "GET", "path": "people/{userId}/badges", "response": {"$ref": "BadgeList"}}, "get": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"userId": {"required": true, "type": "string", "location": "path"}, "badgeId": {"required": true, "type": "string", "location": "path", "format": "int64"}}, "id": "orkut.badges.get", "httpMethod": "GET", "path": "people/{userId}/badges/{badgeId}", "response": {"$ref": "Badge"}}}}', true));
    $this->counters = new CFGS_Google_CountersServiceResource($this, $this->serviceName, 'counters', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/orkut", "https://www.googleapis.com/auth/orkut.readonly"], "parameters": {"userId": {"required": true, "type": "string", "location": "path"}}, "id": "orkut.counters.list", "httpMethod": "GET", "path": "people/{userId}/counters", "response": {"$ref": "Counters"}}}}', true));

  }
}

class CFGS_Google_Acl extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_AclItems';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $description;
  public $totalParticipants;
  public function setItems(/* array(CFGS_Google_AclItems) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_AclItems', __METHOD__);
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
  public function setTotalParticipants($totalParticipants) {
    $this->totalParticipants = $totalParticipants;
  }
  public function getTotalParticipants() {
    return $this->totalParticipants;
  }
}

class CFGS_Google_AclItems extends CFGS_Google_Model {
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

class CFGS_Google_Activity extends CFGS_Google_Model {
  public $kind;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  public $title;
  protected $__objectType = 'CFGS_Google_ActivityObject';
  protected $__objectDataType = '';
  public $object;
  public $updated;
  protected $__actorType = 'CFGS_Google_OrkutAuthorResource';
  protected $__actorDataType = '';
  public $actor;
  protected $__accessType = 'CFGS_Google_Acl';
  protected $__accessDataType = '';
  public $access;
  public $verb;
  public $published;
  public $id;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setObject(CFGS_Google_ActivityObject $object) {
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
  public function setActor(CFGS_Google_OrkutAuthorResource $actor) {
    $this->actor = $actor;
  }
  public function getActor() {
    return $this->actor;
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
}

class CFGS_Google_ActivityList extends CFGS_Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'CFGS_Google_Activity';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(CFGS_Google_Activity) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Activity', __METHOD__);
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
}

class CFGS_Google_ActivityObject extends CFGS_Google_Model {
  public $content;
  protected $__itemsType = 'CFGS_Google_OrkutActivityobjectsResource';
  protected $__itemsDataType = 'array';
  public $items;
  protected $__repliesType = 'CFGS_Google_ActivityObjectReplies';
  protected $__repliesDataType = '';
  public $replies;
  public $objectType;
  public function setContent($content) {
    $this->content = $content;
  }
  public function getContent() {
    return $this->content;
  }
  public function setItems(/* array(CFGS_Google_OrkutActivityobjectsResource) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_OrkutActivityobjectsResource', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setReplies(CFGS_Google_ActivityObjectReplies $replies) {
    $this->replies = $replies;
  }
  public function getReplies() {
    return $this->replies;
  }
  public function setObjectType($objectType) {
    $this->objectType = $objectType;
  }
  public function getObjectType() {
    return $this->objectType;
  }
}

class CFGS_Google_ActivityObjectReplies extends CFGS_Google_Model {
  public $totalItems;
  protected $__itemsType = 'CFGS_Google_Comment';
  protected $__itemsDataType = 'array';
  public $items;
  public $url;
  public function setTotalItems($totalItems) {
    $this->totalItems = $totalItems;
  }
  public function getTotalItems() {
    return $this->totalItems;
  }
  public function setItems(/* array(CFGS_Google_Comment) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Comment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_Badge extends CFGS_Google_Model {
  public $badgeSmallLogo;
  public $kind;
  public $description;
  public $sponsorLogo;
  public $sponsorName;
  public $badgeLargeLogo;
  public $caption;
  public $sponsorUrl;
  public $id;
  public function setBadgeSmallLogo($badgeSmallLogo) {
    $this->badgeSmallLogo = $badgeSmallLogo;
  }
  public function getBadgeSmallLogo() {
    return $this->badgeSmallLogo;
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
  public function setSponsorLogo($sponsorLogo) {
    $this->sponsorLogo = $sponsorLogo;
  }
  public function getSponsorLogo() {
    return $this->sponsorLogo;
  }
  public function setSponsorName($sponsorName) {
    $this->sponsorName = $sponsorName;
  }
  public function getSponsorName() {
    return $this->sponsorName;
  }
  public function setBadgeLargeLogo($badgeLargeLogo) {
    $this->badgeLargeLogo = $badgeLargeLogo;
  }
  public function getBadgeLargeLogo() {
    return $this->badgeLargeLogo;
  }
  public function setCaption($caption) {
    $this->caption = $caption;
  }
  public function getCaption() {
    return $this->caption;
  }
  public function setSponsorUrl($sponsorUrl) {
    $this->sponsorUrl = $sponsorUrl;
  }
  public function getSponsorUrl() {
    return $this->sponsorUrl;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_BadgeList extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_Badge';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(CFGS_Google_Badge) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Badge', __METHOD__);
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
}

class CFGS_Google_Comment extends CFGS_Google_Model {
  protected $__inReplyToType = 'CFGS_Google_CommentInReplyTo';
  protected $__inReplyToDataType = '';
  public $inReplyTo;
  public $kind;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  protected $__actorType = 'CFGS_Google_OrkutAuthorResource';
  protected $__actorDataType = '';
  public $actor;
  public $content;
  public $published;
  public $id;
  public function setInReplyTo(CFGS_Google_CommentInReplyTo $inReplyTo) {
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
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setActor(CFGS_Google_OrkutAuthorResource $actor) {
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
}

class CFGS_Google_CommentInReplyTo extends CFGS_Google_Model {
  public $type;
  public $href;
  public $ref;
  public $rel;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setRef($ref) {
    $this->ref = $ref;
  }
  public function getRef() {
    return $this->ref;
  }
  public function setRel($rel) {
    $this->rel = $rel;
  }
  public function getRel() {
    return $this->rel;
  }
}

class CFGS_Google_CommentList extends CFGS_Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'CFGS_Google_Comment';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $previousPageToken;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(CFGS_Google_Comment) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Comment', __METHOD__);
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
  public function setPreviousPageToken($previousPageToken) {
    $this->previousPageToken = $previousPageToken;
  }
  public function getPreviousPageToken() {
    return $this->previousPageToken;
  }
}

class CFGS_Google_Community extends CFGS_Google_Model {
  public $category;
  public $kind;
  public $member_count;
  public $description;
  public $language;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  public $creation_date;
  protected $__ownerType = 'CFGS_Google_OrkutAuthorResource';
  protected $__ownerDataType = '';
  public $owner;
  protected $__moderatorsType = 'CFGS_Google_OrkutAuthorResource';
  protected $__moderatorsDataType = 'array';
  public $moderators;
  public $location;
  protected $__co_ownersType = 'CFGS_Google_OrkutAuthorResource';
  protected $__co_ownersDataType = 'array';
  public $co_owners;
  public $photo_url;
  public $id;
  public $name;
  public function setCategory($category) {
    $this->category = $category;
  }
  public function getCategory() {
    return $this->category;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setMember_count($member_count) {
    $this->member_count = $member_count;
  }
  public function getMember_count() {
    return $this->member_count;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setLanguage($language) {
    $this->language = $language;
  }
  public function getLanguage() {
    return $this->language;
  }
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setCreation_date($creation_date) {
    $this->creation_date = $creation_date;
  }
  public function getCreation_date() {
    return $this->creation_date;
  }
  public function setOwner(CFGS_Google_OrkutAuthorResource $owner) {
    $this->owner = $owner;
  }
  public function getOwner() {
    return $this->owner;
  }
  public function setModerators(/* array(CFGS_Google_OrkutAuthorResource) */ $moderators) {
    $this->assertIsArray($moderators, 'CFGS_Google_OrkutAuthorResource', __METHOD__);
    $this->moderators = $moderators;
  }
  public function getModerators() {
    return $this->moderators;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setCo_owners(/* array(CFGS_Google_OrkutAuthorResource) */ $co_owners) {
    $this->assertIsArray($co_owners, 'CFGS_Google_OrkutAuthorResource', __METHOD__);
    $this->co_owners = $co_owners;
  }
  public function getCo_owners() {
    return $this->co_owners;
  }
  public function setPhoto_url($photo_url) {
    $this->photo_url = $photo_url;
  }
  public function getPhoto_url() {
    return $this->photo_url;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_CommunityList extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_Community';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(CFGS_Google_Community) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Community', __METHOD__);
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
}

class CFGS_Google_CommunityMembers extends CFGS_Google_Model {
  protected $__communityMembershipStatusType = 'CFGS_Google_CommunityMembershipStatus';
  protected $__communityMembershipStatusDataType = '';
  public $communityMembershipStatus;
  protected $__personType = 'CFGS_Google_OrkutActivitypersonResource';
  protected $__personDataType = '';
  public $person;
  public $kind;
  public function setCommunityMembershipStatus(CFGS_Google_CommunityMembershipStatus $communityMembershipStatus) {
    $this->communityMembershipStatus = $communityMembershipStatus;
  }
  public function getCommunityMembershipStatus() {
    return $this->communityMembershipStatus;
  }
  public function setPerson(CFGS_Google_OrkutActivitypersonResource $person) {
    $this->person = $person;
  }
  public function getPerson() {
    return $this->person;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class CFGS_Google_CommunityMembersList extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_CommunityMembers';
  protected $__itemsDataType = 'array';
  public $items;
  public $prevPageToken;
  public $lastPageToken;
  public $firstPageToken;
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
  public function setItems(/* array(CFGS_Google_CommunityMembers) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_CommunityMembers', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setLastPageToken($lastPageToken) {
    $this->lastPageToken = $lastPageToken;
  }
  public function getLastPageToken() {
    return $this->lastPageToken;
  }
  public function setFirstPageToken($firstPageToken) {
    $this->firstPageToken = $firstPageToken;
  }
  public function getFirstPageToken() {
    return $this->firstPageToken;
  }
}

class CFGS_Google_CommunityMembershipStatus extends CFGS_Google_Model {
  public $status;
  public $isFollowing;
  public $isRestoreAvailable;
  public $isModerator;
  public $kind;
  public $isCoOwner;
  public $canCreatePoll;
  public $canShout;
  public $isOwner;
  public $canCreateTopic;
  public $isTakebackAvailable;
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setIsFollowing($isFollowing) {
    $this->isFollowing = $isFollowing;
  }
  public function getIsFollowing() {
    return $this->isFollowing;
  }
  public function setIsRestoreAvailable($isRestoreAvailable) {
    $this->isRestoreAvailable = $isRestoreAvailable;
  }
  public function getIsRestoreAvailable() {
    return $this->isRestoreAvailable;
  }
  public function setIsModerator($isModerator) {
    $this->isModerator = $isModerator;
  }
  public function getIsModerator() {
    return $this->isModerator;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setIsCoOwner($isCoOwner) {
    $this->isCoOwner = $isCoOwner;
  }
  public function getIsCoOwner() {
    return $this->isCoOwner;
  }
  public function setCanCreatePoll($canCreatePoll) {
    $this->canCreatePoll = $canCreatePoll;
  }
  public function getCanCreatePoll() {
    return $this->canCreatePoll;
  }
  public function setCanShout($canShout) {
    $this->canShout = $canShout;
  }
  public function getCanShout() {
    return $this->canShout;
  }
  public function setIsOwner($isOwner) {
    $this->isOwner = $isOwner;
  }
  public function getIsOwner() {
    return $this->isOwner;
  }
  public function setCanCreateTopic($canCreateTopic) {
    $this->canCreateTopic = $canCreateTopic;
  }
  public function getCanCreateTopic() {
    return $this->canCreateTopic;
  }
  public function setIsTakebackAvailable($isTakebackAvailable) {
    $this->isTakebackAvailable = $isTakebackAvailable;
  }
  public function getIsTakebackAvailable() {
    return $this->isTakebackAvailable;
  }
}

class CFGS_Google_CommunityMessage extends CFGS_Google_Model {
  public $body;
  public $kind;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  protected $__authorType = 'CFGS_Google_OrkutAuthorResource';
  protected $__authorDataType = '';
  public $author;
  public $id;
  public $addedDate;
  public $isSpam;
  public $subject;
  public function setBody($body) {
    $this->body = $body;
  }
  public function getBody() {
    return $this->body;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setAuthor(CFGS_Google_OrkutAuthorResource $author) {
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAddedDate($addedDate) {
    $this->addedDate = $addedDate;
  }
  public function getAddedDate() {
    return $this->addedDate;
  }
  public function setIsSpam($isSpam) {
    $this->isSpam = $isSpam;
  }
  public function getIsSpam() {
    return $this->isSpam;
  }
  public function setSubject($subject) {
    $this->subject = $subject;
  }
  public function getSubject() {
    return $this->subject;
  }
}

class CFGS_Google_CommunityMessageList extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_CommunityMessage';
  protected $__itemsDataType = 'array';
  public $items;
  public $prevPageToken;
  public $lastPageToken;
  public $firstPageToken;
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
  public function setItems(/* array(CFGS_Google_CommunityMessage) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_CommunityMessage', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setLastPageToken($lastPageToken) {
    $this->lastPageToken = $lastPageToken;
  }
  public function getLastPageToken() {
    return $this->lastPageToken;
  }
  public function setFirstPageToken($firstPageToken) {
    $this->firstPageToken = $firstPageToken;
  }
  public function getFirstPageToken() {
    return $this->firstPageToken;
  }
}

class CFGS_Google_CommunityPoll extends CFGS_Google_Model {
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  public $isMultipleAnswers;
  protected $__imageType = 'CFGS_Google_CommunityPollImage';
  protected $__imageDataType = '';
  public $image;
  public $endingTime;
  public $isVotingAllowedForNonMembers;
  public $isSpam;
  public $totalNumberOfVotes;
  protected $__authorType = 'CFGS_Google_OrkutAuthorResource';
  protected $__authorDataType = '';
  public $author;
  public $question;
  public $id;
  public $isRestricted;
  public $communityId;
  public $isUsersVotePublic;
  public $lastUpdate;
  public $description;
  public $votedOptions;
  public $isOpenForVoting;
  public $isClosed;
  public $hasVoted;
  public $kind;
  public $creationTime;
  protected $__optionsType = 'CFGS_Google_OrkutCommunitypolloptionResource';
  protected $__optionsDataType = 'array';
  public $options;
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setIsMultipleAnswers($isMultipleAnswers) {
    $this->isMultipleAnswers = $isMultipleAnswers;
  }
  public function getIsMultipleAnswers() {
    return $this->isMultipleAnswers;
  }
  public function setImage(CFGS_Google_CommunityPollImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setEndingTime($endingTime) {
    $this->endingTime = $endingTime;
  }
  public function getEndingTime() {
    return $this->endingTime;
  }
  public function setIsVotingAllowedForNonMembers($isVotingAllowedForNonMembers) {
    $this->isVotingAllowedForNonMembers = $isVotingAllowedForNonMembers;
  }
  public function getIsVotingAllowedForNonMembers() {
    return $this->isVotingAllowedForNonMembers;
  }
  public function setIsSpam($isSpam) {
    $this->isSpam = $isSpam;
  }
  public function getIsSpam() {
    return $this->isSpam;
  }
  public function setTotalNumberOfVotes($totalNumberOfVotes) {
    $this->totalNumberOfVotes = $totalNumberOfVotes;
  }
  public function getTotalNumberOfVotes() {
    return $this->totalNumberOfVotes;
  }
  public function setAuthor(CFGS_Google_OrkutAuthorResource $author) {
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
  public function setQuestion($question) {
    $this->question = $question;
  }
  public function getQuestion() {
    return $this->question;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setIsRestricted($isRestricted) {
    $this->isRestricted = $isRestricted;
  }
  public function getIsRestricted() {
    return $this->isRestricted;
  }
  public function setCommunityId($communityId) {
    $this->communityId = $communityId;
  }
  public function getCommunityId() {
    return $this->communityId;
  }
  public function setIsUsersVotePublic($isUsersVotePublic) {
    $this->isUsersVotePublic = $isUsersVotePublic;
  }
  public function getIsUsersVotePublic() {
    return $this->isUsersVotePublic;
  }
  public function setLastUpdate($lastUpdate) {
    $this->lastUpdate = $lastUpdate;
  }
  public function getLastUpdate() {
    return $this->lastUpdate;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setVotedOptions(/* array(CFGS_Google_int) */ $votedOptions) {
    $this->assertIsArray($votedOptions, 'CFGS_Google_int', __METHOD__);
    $this->votedOptions = $votedOptions;
  }
  public function getVotedOptions() {
    return $this->votedOptions;
  }
  public function setIsOpenForVoting($isOpenForVoting) {
    $this->isOpenForVoting = $isOpenForVoting;
  }
  public function getIsOpenForVoting() {
    return $this->isOpenForVoting;
  }
  public function setIsClosed($isClosed) {
    $this->isClosed = $isClosed;
  }
  public function getIsClosed() {
    return $this->isClosed;
  }
  public function setHasVoted($hasVoted) {
    $this->hasVoted = $hasVoted;
  }
  public function getHasVoted() {
    return $this->hasVoted;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCreationTime($creationTime) {
    $this->creationTime = $creationTime;
  }
  public function getCreationTime() {
    return $this->creationTime;
  }
  public function setOptions(/* array(CFGS_Google_OrkutCommunitypolloptionResource) */ $options) {
    $this->assertIsArray($options, 'CFGS_Google_OrkutCommunitypolloptionResource', __METHOD__);
    $this->options = $options;
  }
  public function getOptions() {
    return $this->options;
  }
}

class CFGS_Google_CommunityPollComment extends CFGS_Google_Model {
  public $body;
  public $kind;
  public $addedDate;
  public $id;
  protected $__authorType = 'CFGS_Google_OrkutAuthorResource';
  protected $__authorDataType = '';
  public $author;
  public function setBody($body) {
    $this->body = $body;
  }
  public function getBody() {
    return $this->body;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setAddedDate($addedDate) {
    $this->addedDate = $addedDate;
  }
  public function getAddedDate() {
    return $this->addedDate;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAuthor(CFGS_Google_OrkutAuthorResource $author) {
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
}

class CFGS_Google_CommunityPollCommentList extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_CommunityPollComment';
  protected $__itemsDataType = 'array';
  public $items;
  public $prevPageToken;
  public $lastPageToken;
  public $firstPageToken;
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
  public function setItems(/* array(CFGS_Google_CommunityPollComment) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_CommunityPollComment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setLastPageToken($lastPageToken) {
    $this->lastPageToken = $lastPageToken;
  }
  public function getLastPageToken() {
    return $this->lastPageToken;
  }
  public function setFirstPageToken($firstPageToken) {
    $this->firstPageToken = $firstPageToken;
  }
  public function getFirstPageToken() {
    return $this->firstPageToken;
  }
}

class CFGS_Google_CommunityPollImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_CommunityPollList extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_CommunityPoll';
  protected $__itemsDataType = 'array';
  public $items;
  public $prevPageToken;
  public $lastPageToken;
  public $firstPageToken;
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
  public function setItems(/* array(CFGS_Google_CommunityPoll) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_CommunityPoll', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setLastPageToken($lastPageToken) {
    $this->lastPageToken = $lastPageToken;
  }
  public function getLastPageToken() {
    return $this->lastPageToken;
  }
  public function setFirstPageToken($firstPageToken) {
    $this->firstPageToken = $firstPageToken;
  }
  public function getFirstPageToken() {
    return $this->firstPageToken;
  }
}

class CFGS_Google_CommunityPollVote extends CFGS_Google_Model {
  public $kind;
  public $optionIds;
  public $isVotevisible;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setOptionIds(/* array(CFGS_Google_int) */ $optionIds) {
    $this->assertIsArray($optionIds, 'CFGS_Google_int', __METHOD__);
    $this->optionIds = $optionIds;
  }
  public function getOptionIds() {
    return $this->optionIds;
  }
  public function setIsVotevisible($isVotevisible) {
    $this->isVotevisible = $isVotevisible;
  }
  public function getIsVotevisible() {
    return $this->isVotevisible;
  }
}

class CFGS_Google_CommunityTopic extends CFGS_Google_Model {
  public $body;
  public $lastUpdate;
  public $kind;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  protected $__authorType = 'CFGS_Google_OrkutAuthorResource';
  protected $__authorDataType = '';
  public $author;
  public $title;
  protected $__messagesType = 'CFGS_Google_CommunityMessage';
  protected $__messagesDataType = 'array';
  public $messages;
  public $latestMessageSnippet;
  public $isClosed;
  public $numberOfReplies;
  public $id;
  public function setBody($body) {
    $this->body = $body;
  }
  public function getBody() {
    return $this->body;
  }
  public function setLastUpdate($lastUpdate) {
    $this->lastUpdate = $lastUpdate;
  }
  public function getLastUpdate() {
    return $this->lastUpdate;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setAuthor(CFGS_Google_OrkutAuthorResource $author) {
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setMessages(/* array(CFGS_Google_CommunityMessage) */ $messages) {
    $this->assertIsArray($messages, 'CFGS_Google_CommunityMessage', __METHOD__);
    $this->messages = $messages;
  }
  public function getMessages() {
    return $this->messages;
  }
  public function setLatestMessageSnippet($latestMessageSnippet) {
    $this->latestMessageSnippet = $latestMessageSnippet;
  }
  public function getLatestMessageSnippet() {
    return $this->latestMessageSnippet;
  }
  public function setIsClosed($isClosed) {
    $this->isClosed = $isClosed;
  }
  public function getIsClosed() {
    return $this->isClosed;
  }
  public function setNumberOfReplies($numberOfReplies) {
    $this->numberOfReplies = $numberOfReplies;
  }
  public function getNumberOfReplies() {
    return $this->numberOfReplies;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_CommunityTopicList extends CFGS_Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'CFGS_Google_CommunityTopic';
  protected $__itemsDataType = 'array';
  public $items;
  public $prevPageToken;
  public $lastPageToken;
  public $firstPageToken;
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
  public function setItems(/* array(CFGS_Google_CommunityTopic) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_CommunityTopic', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setPrevPageToken($prevPageToken) {
    $this->prevPageToken = $prevPageToken;
  }
  public function getPrevPageToken() {
    return $this->prevPageToken;
  }
  public function setLastPageToken($lastPageToken) {
    $this->lastPageToken = $lastPageToken;
  }
  public function getLastPageToken() {
    return $this->lastPageToken;
  }
  public function setFirstPageToken($firstPageToken) {
    $this->firstPageToken = $firstPageToken;
  }
  public function getFirstPageToken() {
    return $this->firstPageToken;
  }
}

class CFGS_Google_Counters extends CFGS_Google_Model {
  protected $__itemsType = 'CFGS_Google_OrkutCounterResource';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(CFGS_Google_OrkutCounterResource) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_OrkutCounterResource', __METHOD__);
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
}

class CFGS_Google_OrkutActivityobjectsResource extends CFGS_Google_Model {
  public $displayName;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  protected $__communityType = 'CFGS_Google_Community';
  protected $__communityDataType = '';
  public $community;
  public $content;
  protected $__personType = 'CFGS_Google_OrkutActivitypersonResource';
  protected $__personDataType = '';
  public $person;
  public $id;
  public $objectType;
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setCommunity(CFGS_Google_Community $community) {
    $this->community = $community;
  }
  public function getCommunity() {
    return $this->community;
  }
  public function setContent($content) {
    $this->content = $content;
  }
  public function getContent() {
    return $this->content;
  }
  public function setPerson(CFGS_Google_OrkutActivitypersonResource $person) {
    $this->person = $person;
  }
  public function getPerson() {
    return $this->person;
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

class CFGS_Google_OrkutActivitypersonResource extends CFGS_Google_Model {
  protected $__nameType = 'CFGS_Google_OrkutActivitypersonResourceName';
  protected $__nameDataType = '';
  public $name;
  public $url;
  public $gender;
  protected $__imageType = 'CFGS_Google_OrkutActivitypersonResourceImage';
  protected $__imageDataType = '';
  public $image;
  public $birthday;
  public $id;
  public function setName(CFGS_Google_OrkutActivitypersonResourceName $name) {
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
  public function setImage(CFGS_Google_OrkutActivitypersonResourceImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setBirthday($birthday) {
    $this->birthday = $birthday;
  }
  public function getBirthday() {
    return $this->birthday;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class CFGS_Google_OrkutActivitypersonResourceImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_OrkutActivitypersonResourceName extends CFGS_Google_Model {
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

class CFGS_Google_OrkutAuthorResource extends CFGS_Google_Model {
  public $url;
  protected $__imageType = 'CFGS_Google_OrkutAuthorResourceImage';
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
  public function setImage(CFGS_Google_OrkutAuthorResourceImage $image) {
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

class CFGS_Google_OrkutAuthorResourceImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_OrkutCommunitypolloptionResource extends CFGS_Google_Model {
  protected $__imageType = 'CFGS_Google_OrkutCommunitypolloptionResourceImage';
  protected $__imageDataType = '';
  public $image;
  public $optionId;
  public $description;
  public $numberOfVotes;
  public function setImage(CFGS_Google_OrkutCommunitypolloptionResourceImage $image) {
    $this->image = $image;
  }
  public function getImage() {
    return $this->image;
  }
  public function setOptionId($optionId) {
    $this->optionId = $optionId;
  }
  public function getOptionId() {
    return $this->optionId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setNumberOfVotes($numberOfVotes) {
    $this->numberOfVotes = $numberOfVotes;
  }
  public function getNumberOfVotes() {
    return $this->numberOfVotes;
  }
}

class CFGS_Google_OrkutCommunitypolloptionResourceImage extends CFGS_Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class CFGS_Google_OrkutCounterResource extends CFGS_Google_Model {
  public $total;
  protected $__linkType = 'CFGS_Google_OrkutLinkResource';
  protected $__linkDataType = '';
  public $link;
  public $name;
  public function setTotal($total) {
    $this->total = $total;
  }
  public function getTotal() {
    return $this->total;
  }
  public function setLink(CFGS_Google_OrkutLinkResource $link) {
    $this->link = $link;
  }
  public function getLink() {
    return $this->link;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_OrkutLinkResource extends CFGS_Google_Model {
  public $href;
  public $type;
  public $rel;
  public $title;
  public function setHref($href) {
    $this->href = $href;
  }
  public function getHref() {
    return $this->href;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setRel($rel) {
    $this->rel = $rel;
  }
  public function getRel() {
    return $this->rel;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
}

class CFGS_Google_Visibility extends CFGS_Google_Model {
  public $kind;
  public $visibility;
  protected $__linksType = 'CFGS_Google_OrkutLinkResource';
  protected $__linksDataType = 'array';
  public $links;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setVisibility($visibility) {
    $this->visibility = $visibility;
  }
  public function getVisibility() {
    return $this->visibility;
  }
  public function setLinks(/* array(CFGS_Google_OrkutLinkResource) */ $links) {
    $this->assertIsArray($links, 'CFGS_Google_OrkutLinkResource', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
}
