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
   * The "management" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $management = $analyticsService->management;
   *  </code>
   */
  class CFGS_Google_ManagementServiceResource extends CFGS_Google_ServiceResource {


  }

  /**
   * The "dailyUploads" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $dailyUploads = $analyticsService->dailyUploads;
   *  </code>
   */
  class CFGS_Google_ManagementDailyUploadsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * List daily uploads to which the user has access. (dailyUploads.list)
     *
     * @param string $accountId Account Id for the daily uploads to retrieve.
     * @param string $webPropertyId Web property Id for the daily uploads to retrieve.
     * @param string $customDataSourceId Custom data source Id for daily uploads to retrieve.
     * @param string $start_date Start date of the form YYYY-MM-DD.
     * @param string $end_date End date of the form YYYY-MM-DD.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of custom data sources to include in this response.
     * @opt_param int start-index A 1-based index of the first daily upload to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_DailyUploads
     */
    public function listManagementDailyUploads($accountId, $webPropertyId, $customDataSourceId, $start_date, $end_date, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'customDataSourceId' => $customDataSourceId, 'start-date' => $start_date, 'end-date' => $end_date);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_DailyUploads($data);
      } else {
        return $data;
      }
    }
    /**
     * Update/Overwrite data for a custom data source. (dailyUploads.upload)
     *
     * @param string $accountId Account Id associated with daily upload.
     * @param string $webPropertyId Web property Id associated with daily upload.
     * @param string $customDataSourceId Custom data source Id to which the data being uploaded belongs.
     * @param string $date Date for which data is uploaded. Date should be formatted as YYYY-MM-DD.
     * @param int $appendNumber Append number for this upload indexed from 1.
     * @param string $type Type of data for this upload.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool reset Reset/Overwrite all previous appends for this date and start over with this file as the first upload.
     * @return CFGS_Google_DailyUploadAppend
     */
    public function upload($accountId, $webPropertyId, $customDataSourceId, $date, $appendNumber, $type, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'customDataSourceId' => $customDataSourceId, 'date' => $date, 'appendNumber' => $appendNumber, 'type' => $type);
      $params = array_merge($params, $optParams);
      $data = $this->__call('upload', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_DailyUploadAppend($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete uploaded data for the given date. (dailyUploads.delete)
     *
     * @param string $accountId Account Id associated with daily upload delete.
     * @param string $webPropertyId Web property Id associated with daily upload delete.
     * @param string $customDataSourceId Custom data source Id associated with daily upload delete.
     * @param string $date Date for which data is to be deleted. Date should be formatted as YYYY-MM-DD.
     * @param string $type Type of data for this delete.
     * @param array $optParams Optional parameters.
     */
    public function delete($accountId, $webPropertyId, $customDataSourceId, $date, $type, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'customDataSourceId' => $customDataSourceId, 'date' => $date, 'type' => $type);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }
  /**
   * The "segments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $segments = $analyticsService->segments;
   *  </code>
   */
  class CFGS_Google_ManagementSegmentsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Lists advanced segments to which the user has access. (segments.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of advanced segments to include in this response.
     * @opt_param int start-index An index of the first advanced segment to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_Segments
     */
    public function listManagementSegments($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Segments($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "profiles" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $profiles = $analyticsService->profiles;
   *  </code>
   */
  class CFGS_Google_ManagementProfilesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Lists profiles to which the user has access. (profiles.list)
     *
     * @param string $accountId Account ID for the profiles to retrieve. Can either be a specific account ID or '~all', which refers to all the accounts to which the user has access.
     * @param string $webPropertyId Web property ID for the profiles to retrieve. Can either be a specific web property ID or '~all', which refers to all the web properties to which the user has access.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of profiles to include in this response.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_Profiles
     */
    public function listManagementProfiles($accountId, $webPropertyId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Profiles($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "customDataSources" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $customDataSources = $analyticsService->customDataSources;
   *  </code>
   */
  class CFGS_Google_ManagementCustomDataSourcesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * List custom data sources to which the user has access. (customDataSources.list)
     *
     * @param string $accountId Account Id for the custom data sources to retrieve.
     * @param string $webPropertyId Web property Id for the custom data sources to retrieve.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of custom data sources to include in this response.
     * @opt_param int start-index A 1-based index of the first custom data source to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_CustomDataSources
     */
    public function listManagementCustomDataSources($accountId, $webPropertyId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_CustomDataSources($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "goals" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $goals = $analyticsService->goals;
   *  </code>
   */
  class CFGS_Google_ManagementGoalsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Lists goals to which the user has access. (goals.list)
     *
     * @param string $accountId Account ID to retrieve goals for. Can either be a specific account ID or '~all', which refers to all the accounts that user has access to.
     * @param string $webPropertyId Web property ID to retrieve goals for. Can either be a specific web property ID or '~all', which refers to all the web properties that user has access to.
     * @param string $profileId Profile ID to retrieve goals for. Can either be a specific profile ID or '~all', which refers to all the profiles that user has access to.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of goals to include in this response.
     * @opt_param int start-index An index of the first goal to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_Goals
     */
    public function listManagementGoals($accountId, $webPropertyId, $profileId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'webPropertyId' => $webPropertyId, 'profileId' => $profileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Goals($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "accounts" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $accounts = $analyticsService->accounts;
   *  </code>
   */
  class CFGS_Google_ManagementAccountsServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Lists all accounts to which the user has access. (accounts.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of accounts to include in this response.
     * @opt_param int start-index An index of the first account to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_Accounts
     */
    public function listManagementAccounts($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Accounts($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "webproperties" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $webproperties = $analyticsService->webproperties;
   *  </code>
   */
  class CFGS_Google_ManagementWebpropertiesServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Lists web properties to which the user has access. (webproperties.list)
     *
     * @param string $accountId Account ID to retrieve web properties for. Can either be a specific account ID or '~all', which refers to all the accounts that user has access to.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of web properties to include in this response.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @return CFGS_Google_Webproperties
     */
    public function listManagementWebproperties($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_Webproperties($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "data" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $data = $analyticsService->data;
   *  </code>
   */
  class CFGS_Google_DataServiceResource extends CFGS_Google_ServiceResource {


  }

  /**
   * The "mcf" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $mcf = $analyticsService->mcf;
   *  </code>
   */
  class CFGS_Google_DataMcfServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Returns Analytics Multi-Channel Funnels data for a profile. (mcf.get)
     *
     * @param string $ids Unique table ID for retrieving Analytics data. Table ID is of the form ga:XXXX, where XXXX is the Analytics profile ID.
     * @param string $start_date Start date for fetching Analytics data. All requests should specify a start date formatted as YYYY-MM-DD.
     * @param string $end_date End date for fetching Analytics data. All requests should specify an end date formatted as YYYY-MM-DD.
     * @param string $metrics A comma-separated list of Multi-Channel Funnels metrics. E.g., 'mcf:totalConversions,mcf:totalConversionValue'. At least one metric must be specified.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of entries to include in this feed.
     * @opt_param string sort A comma-separated list of dimensions or metrics that determine the sort order for the Analytics data.
     * @opt_param string dimensions A comma-separated list of Multi-Channel Funnels dimensions. E.g., 'mcf:source,mcf:medium'.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @opt_param string filters A comma-separated list of dimension or metric filters to be applied to the Analytics data.
     * @return CFGS_Google_McfData
     */
    public function get($ids, $start_date, $end_date, $metrics, $optParams = array()) {
      $params = array('ids' => $ids, 'start-date' => $start_date, 'end-date' => $end_date, 'metrics' => $metrics);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_McfData($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "ga" collection of methods.
   * Typical usage is:
   *  <code>
   *   $analyticsService = new CFGS_Google_AnalyticsService(...);
   *   $ga = $analyticsService->ga;
   *  </code>
   */
  class CFGS_Google_DataGaServiceResource extends CFGS_Google_ServiceResource {


    /**
     * Returns Analytics data for a profile. (ga.get)
     *
     * @param string $ids Unique table ID for retrieving Analytics data. Table ID is of the form ga:XXXX, where XXXX is the Analytics profile ID.
     * @param string $start_date Start date for fetching Analytics data. All requests should specify a start date formatted as YYYY-MM-DD.
     * @param string $end_date End date for fetching Analytics data. All requests should specify an end date formatted as YYYY-MM-DD.
     * @param string $metrics A comma-separated list of Analytics metrics. E.g., 'ga:visits,ga:pageviews'. At least one metric must be specified.
     * @param array $optParams Optional parameters.
     *
     * @opt_param int max-results The maximum number of entries to include in this feed.
     * @opt_param string sort A comma-separated list of dimensions or metrics that determine the sort order for Analytics data.
     * @opt_param string dimensions A comma-separated list of Analytics dimensions. E.g., 'ga:browser,ga:city'.
     * @opt_param int start-index An index of the first entity to retrieve. Use this parameter as a pagination mechanism along with the max-results parameter.
     * @opt_param string segment An Analytics advanced segment to be applied to data.
     * @opt_param string filters A comma-separated list of dimension or metric filters to be applied to Analytics data.
     * @return CFGS_Google_GaData
     */
    public function get($ids, $start_date, $end_date, $metrics, $optParams = array()) {
      $params = array('ids' => $ids, 'start-date' => $start_date, 'end-date' => $end_date, 'metrics' => $metrics);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new CFGS_Google_GaData($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for CFGS_Google_Analytics (v3).
 *
 * <p>
 * View and manage your Google Analytics data
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/analytics/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class CFGS_Google_AnalyticsService extends CFGS_Google_Service {
  public $management_dailyUploads;
  public $management_segments;
  public $management_profiles;
  public $management_customDataSources;
  public $management_goals;
  public $management_accounts;
  public $management_webproperties;
  public $data_mcf;
  public $data_ga;
  /**
   * Constructs the internal representation of the Analytics service.
   *
   * @param CFGS_Google_Client $client
   */
  public function __construct(CFGS_Google_Client $client) {
    $this->servicePath = 'analytics/v3/';
    $this->version = 'v3';
    $this->serviceName = 'analytics';

    $client->addService($this->serviceName, $this->version);
    $this->management_dailyUploads = new CFGS_Google_ManagementDailyUploadsServiceResource($this, $this->serviceName, 'dailyUploads', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "start-date": {"required": true, "type": "string", "location": "query"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "customDataSourceId": {"required": true, "type": "string", "location": "path"}, "webPropertyId": {"required": true, "type": "string", "location": "path"}, "end-date": {"required": true, "type": "string", "location": "query"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "analytics.management.dailyUploads.list", "httpMethod": "GET", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads", "response": {"$ref": "DailyUploads"}}, "upload": {"scopes": ["https://www.googleapis.com/auth/analytics"], "parameters": {"reset": {"default": "false", "type": "boolean", "location": "query"}, "customDataSourceId": {"required": true, "type": "string", "location": "path"}, "appendNumber": {"format": "int32", "required": true, "maximum": "20", "minimum": "1", "location": "query", "type": "integer"}, "webPropertyId": {"required": true, "type": "string", "location": "path"}, "date": {"required": true, "type": "string", "location": "path"}, "type": {"required": true, "type": "string", "location": "query", "enum": ["cost"]}, "accountId": {"required": true, "type": "string", "location": "path"}}, "supportsMediaUpload": true, "mediaUpload": {"maxSize": "5MB", "protocols": {"simple": {"path": "/upload/analytics/v3/management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}/uploads", "multipart": true}, "resumable": {"path": "/resumable/upload/analytics/v3/management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}/uploads", "multipart": true}}, "accept": ["application/octet-stream"]}, "response": {"$ref": "DailyUploadAppend"}, "httpMethod": "POST", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}/uploads", "id": "analytics.management.dailyUploads.upload"}, "delete": {"scopes": ["https://www.googleapis.com/auth/analytics"], "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources/{customDataSourceId}/dailyUploads/{date}", "id": "analytics.management.dailyUploads.delete", "parameters": {"date": {"required": true, "type": "string", "location": "path"}, "customDataSourceId": {"required": true, "type": "string", "location": "path"}, "type": {"required": true, "type": "string", "location": "query", "enum": ["cost"]}, "webPropertyId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->management_segments = new CFGS_Google_ManagementSegmentsServiceResource($this, $this->serviceName, 'segments', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}}, "response": {"$ref": "Segments"}, "httpMethod": "GET", "path": "management/segments", "id": "analytics.management.segments.list"}}}', true));
    $this->management_profiles = new CFGS_Google_ManagementProfilesServiceResource($this, $this->serviceName, 'profiles', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "webPropertyId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "analytics.management.profiles.list", "httpMethod": "GET", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles", "response": {"$ref": "Profiles"}}}}', true));
    $this->management_customDataSources = new CFGS_Google_ManagementCustomDataSourcesServiceResource($this, $this->serviceName, 'customDataSources', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "webPropertyId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "analytics.management.customDataSources.list", "httpMethod": "GET", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/customDataSources", "response": {"$ref": "CustomDataSources"}}}}', true));
    $this->management_goals = new CFGS_Google_ManagementGoalsServiceResource($this, $this->serviceName, 'goals', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "profileId": {"required": true, "type": "string", "location": "path"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "webPropertyId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "analytics.management.goals.list", "httpMethod": "GET", "path": "management/accounts/{accountId}/webproperties/{webPropertyId}/profiles/{profileId}/goals", "response": {"$ref": "Goals"}}}}', true));
    $this->management_accounts = new CFGS_Google_ManagementAccountsServiceResource($this, $this->serviceName, 'accounts', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}}, "response": {"$ref": "Accounts"}, "httpMethod": "GET", "path": "management/accounts", "id": "analytics.management.accounts.list"}}}', true));
    $this->management_webproperties = new CFGS_Google_ManagementWebpropertiesServiceResource($this, $this->serviceName, 'webproperties', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "analytics.management.webproperties.list", "httpMethod": "GET", "path": "management/accounts/{accountId}/webproperties", "response": {"$ref": "Webproperties"}}}}', true));
    $this->data_mcf = new CFGS_Google_DataMcfServiceResource($this, $this->serviceName, 'mcf', json_decode('{"methods": {"get": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "sort": {"type": "string", "location": "query"}, "dimensions": {"type": "string", "location": "query"}, "start-date": {"required": true, "type": "string", "location": "query"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "ids": {"required": true, "type": "string", "location": "query"}, "metrics": {"required": true, "type": "string", "location": "query"}, "filters": {"type": "string", "location": "query"}, "end-date": {"required": true, "type": "string", "location": "query"}}, "id": "analytics.data.mcf.get", "httpMethod": "GET", "path": "data/mcf", "response": {"$ref": "McfData"}}}}', true));
    $this->data_ga = new CFGS_Google_DataGaServiceResource($this, $this->serviceName, 'ga', json_decode('{"methods": {"get": {"scopes": ["https://www.googleapis.com/auth/analytics", "https://www.googleapis.com/auth/analytics.readonly"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "int32"}, "sort": {"type": "string", "location": "query"}, "dimensions": {"type": "string", "location": "query"}, "start-date": {"required": true, "type": "string", "location": "query"}, "start-index": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "segment": {"type": "string", "location": "query"}, "ids": {"required": true, "type": "string", "location": "query"}, "metrics": {"required": true, "type": "string", "location": "query"}, "filters": {"type": "string", "location": "query"}, "end-date": {"required": true, "type": "string", "location": "query"}}, "id": "analytics.data.ga.get", "httpMethod": "GET", "path": "data/ga", "response": {"$ref": "GaData"}}}}', true));

  }
}

class CFGS_Google_Account extends CFGS_Google_Model {
  public $kind;
  public $name;
  public $created;
  public $updated;
  protected $__childLinkType = 'CFGS_Google_AccountChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $id;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setChildLink(CFGS_Google_AccountChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
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

class CFGS_Google_AccountChildLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_Accounts extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Account';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_Account) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Account', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_CustomDataSource extends CFGS_Google_Model {
  public $kind;
  public $description;
  public $created;
  public $profilesLinked;
  public $updated;
  public $name;
  protected $__childLinkType = 'CFGS_Google_CustomDataSourceChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $webPropertyId;
  protected $__parentLinkType = 'CFGS_Google_CustomDataSourceParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $id;
  public $selfLink;
  public $accountId;
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
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setProfilesLinked($profilesLinked) {
    $this->profilesLinked = $profilesLinked;
  }
  public function getProfilesLinked() {
    return $this->profilesLinked;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setChildLink(CFGS_Google_CustomDataSourceChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setParentLink(CFGS_Google_CustomDataSourceParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
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
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_CustomDataSourceChildLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_CustomDataSourceParentLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_CustomDataSources extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_CustomDataSource';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_CustomDataSource) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_CustomDataSource', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_DailyUpload extends CFGS_Google_Model {
  protected $__recentChangesType = 'CFGS_Google_DailyUploadRecentChanges';
  protected $__recentChangesDataType = 'array';
  public $recentChanges;
  public $kind;
  public $modifiedTime;
  public $appendCount;
  public $customDataSourceId;
  public $date;
  public $webPropertyId;
  public $createdTime;
  protected $__parentLinkType = 'CFGS_Google_DailyUploadParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $selfLink;
  public $accountId;
  public function setRecentChanges(/* array(CFGS_Google_DailyUploadRecentChanges) */ $recentChanges) {
    $this->assertIsArray($recentChanges, 'CFGS_Google_DailyUploadRecentChanges', __METHOD__);
    $this->recentChanges = $recentChanges;
  }
  public function getRecentChanges() {
    return $this->recentChanges;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setModifiedTime($modifiedTime) {
    $this->modifiedTime = $modifiedTime;
  }
  public function getModifiedTime() {
    return $this->modifiedTime;
  }
  public function setAppendCount($appendCount) {
    $this->appendCount = $appendCount;
  }
  public function getAppendCount() {
    return $this->appendCount;
  }
  public function setCustomDataSourceId($customDataSourceId) {
    $this->customDataSourceId = $customDataSourceId;
  }
  public function getCustomDataSourceId() {
    return $this->customDataSourceId;
  }
  public function setDate($date) {
    $this->date = $date;
  }
  public function getDate() {
    return $this->date;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setCreatedTime($createdTime) {
    $this->createdTime = $createdTime;
  }
  public function getCreatedTime() {
    return $this->createdTime;
  }
  public function setParentLink(CFGS_Google_DailyUploadParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_DailyUploadAppend extends CFGS_Google_Model {
  public $kind;
  public $customDataSourceId;
  public $appendNumber;
  public $webPropertyId;
  public $date;
  public $nextAppendLink;
  public $accountId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCustomDataSourceId($customDataSourceId) {
    $this->customDataSourceId = $customDataSourceId;
  }
  public function getCustomDataSourceId() {
    return $this->customDataSourceId;
  }
  public function setAppendNumber($appendNumber) {
    $this->appendNumber = $appendNumber;
  }
  public function getAppendNumber() {
    return $this->appendNumber;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setDate($date) {
    $this->date = $date;
  }
  public function getDate() {
    return $this->date;
  }
  public function setNextAppendLink($nextAppendLink) {
    $this->nextAppendLink = $nextAppendLink;
  }
  public function getNextAppendLink() {
    return $this->nextAppendLink;
  }
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_DailyUploadParentLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_DailyUploadRecentChanges extends CFGS_Google_Model {
  public $change;
  public $time;
  public function setChange($change) {
    $this->change = $change;
  }
  public function getChange() {
    return $this->change;
  }
  public function setTime($time) {
    $this->time = $time;
  }
  public function getTime() {
    return $this->time;
  }
}

class CFGS_Google_DailyUploads extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_DailyUpload';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_DailyUpload) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_DailyUpload', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_GaData extends CFGS_Google_Model {
  public $kind;
  public $rows;
  public $containsSampledData;
  public $totalResults;
  public $itemsPerPage;
  public $totalsForAllResults;
  public $nextLink;
  public $id;
  protected $__queryType = 'CFGS_Google_GaDataQuery';
  protected $__queryDataType = '';
  public $query;
  public $previousLink;
  protected $__profileInfoType = 'CFGS_Google_GaDataProfileInfo';
  protected $__profileInfoDataType = '';
  public $profileInfo;
  protected $__columnHeadersType = 'CFGS_Google_GaDataColumnHeaders';
  protected $__columnHeadersDataType = 'array';
  public $columnHeaders;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows($rows) {
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setContainsSampledData($containsSampledData) {
    $this->containsSampledData = $containsSampledData;
  }
  public function getContainsSampledData() {
    return $this->containsSampledData;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setTotalsForAllResults($totalsForAllResults) {
    $this->totalsForAllResults = $totalsForAllResults;
  }
  public function getTotalsForAllResults() {
    return $this->totalsForAllResults;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setQuery(CFGS_Google_GaDataQuery $query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setProfileInfo(CFGS_Google_GaDataProfileInfo $profileInfo) {
    $this->profileInfo = $profileInfo;
  }
  public function getProfileInfo() {
    return $this->profileInfo;
  }
  public function setColumnHeaders(/* array(CFGS_Google_GaDataColumnHeaders) */ $columnHeaders) {
    $this->assertIsArray($columnHeaders, 'CFGS_Google_GaDataColumnHeaders', __METHOD__);
    $this->columnHeaders = $columnHeaders;
  }
  public function getColumnHeaders() {
    return $this->columnHeaders;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_GaDataColumnHeaders extends CFGS_Google_Model {
  public $dataType;
  public $columnType;
  public $name;
  public function setDataType($dataType) {
    $this->dataType = $dataType;
  }
  public function getDataType() {
    return $this->dataType;
  }
  public function setColumnType($columnType) {
    $this->columnType = $columnType;
  }
  public function getColumnType() {
    return $this->columnType;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_GaDataProfileInfo extends CFGS_Google_Model {
  public $webPropertyId;
  public $internalWebPropertyId;
  public $tableId;
  public $profileId;
  public $profileName;
  public $accountId;
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setProfileName($profileName) {
    $this->profileName = $profileName;
  }
  public function getProfileName() {
    return $this->profileName;
  }
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_GaDataQuery extends CFGS_Google_Model {
  public $max_results;
  public $sort;
  public $dimensions;
  public $start_date;
  public $start_index;
  public $segment;
  public $ids;
  public $metrics;
  public $filters;
  public $end_date;
  public function setMax_results($max_results) {
    $this->max_results = $max_results;
  }
  public function getMax_results() {
    return $this->max_results;
  }
  public function setSort($sort) {
    $this->sort = $sort;
  }
  public function getSort() {
    return $this->sort;
  }
  public function setDimensions($dimensions) {
    $this->dimensions = $dimensions;
  }
  public function getDimensions() {
    return $this->dimensions;
  }
  public function setStart_date($start_date) {
    $this->start_date = $start_date;
  }
  public function getStart_date() {
    return $this->start_date;
  }
  public function setStart_index($start_index) {
    $this->start_index = $start_index;
  }
  public function getStart_index() {
    return $this->start_index;
  }
  public function setSegment($segment) {
    $this->segment = $segment;
  }
  public function getSegment() {
    return $this->segment;
  }
  public function setIds($ids) {
    $this->ids = $ids;
  }
  public function getIds() {
    return $this->ids;
  }
  public function setMetrics($metrics) {
    $this->metrics = $metrics;
  }
  public function getMetrics() {
    return $this->metrics;
  }
  public function setFilters($filters) {
    $this->filters = $filters;
  }
  public function getFilters() {
    return $this->filters;
  }
  public function setEnd_date($end_date) {
    $this->end_date = $end_date;
  }
  public function getEnd_date() {
    return $this->end_date;
  }
}

class CFGS_Google_Goal extends CFGS_Google_Model {
  public $kind;
  protected $__visitTimeOnSiteDetailsType = 'CFGS_Google_GoalVisitTimeOnSiteDetails';
  protected $__visitTimeOnSiteDetailsDataType = '';
  public $visitTimeOnSiteDetails;
  public $name;
  public $created;
  protected $__urlDestinationDetailsType = 'CFGS_Google_GoalUrlDestinationDetails';
  protected $__urlDestinationDetailsDataType = '';
  public $urlDestinationDetails;
  public $updated;
  public $value;
  protected $__visitNumPagesDetailsType = 'CFGS_Google_GoalVisitNumPagesDetails';
  protected $__visitNumPagesDetailsDataType = '';
  public $visitNumPagesDetails;
  public $internalWebPropertyId;
  protected $__eventDetailsType = 'CFGS_Google_GoalEventDetails';
  protected $__eventDetailsDataType = '';
  public $eventDetails;
  public $webPropertyId;
  public $active;
  public $profileId;
  protected $__parentLinkType = 'CFGS_Google_GoalParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $type;
  public $id;
  public $selfLink;
  public $accountId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setVisitTimeOnSiteDetails(CFGS_Google_GoalVisitTimeOnSiteDetails $visitTimeOnSiteDetails) {
    $this->visitTimeOnSiteDetails = $visitTimeOnSiteDetails;
  }
  public function getVisitTimeOnSiteDetails() {
    return $this->visitTimeOnSiteDetails;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setUrlDestinationDetails(CFGS_Google_GoalUrlDestinationDetails $urlDestinationDetails) {
    $this->urlDestinationDetails = $urlDestinationDetails;
  }
  public function getUrlDestinationDetails() {
    return $this->urlDestinationDetails;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
  public function setVisitNumPagesDetails(CFGS_Google_GoalVisitNumPagesDetails $visitNumPagesDetails) {
    $this->visitNumPagesDetails = $visitNumPagesDetails;
  }
  public function getVisitNumPagesDetails() {
    return $this->visitNumPagesDetails;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setEventDetails(CFGS_Google_GoalEventDetails $eventDetails) {
    $this->eventDetails = $eventDetails;
  }
  public function getEventDetails() {
    return $this->eventDetails;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setActive($active) {
    $this->active = $active;
  }
  public function getActive() {
    return $this->active;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setParentLink(CFGS_Google_GoalParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
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
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_GoalEventDetails extends CFGS_Google_Model {
  protected $__eventConditionsType = 'CFGS_Google_GoalEventDetailsEventConditions';
  protected $__eventConditionsDataType = 'array';
  public $eventConditions;
  public $useEventValue;
  public function setEventConditions(/* array(CFGS_Google_GoalEventDetailsEventConditions) */ $eventConditions) {
    $this->assertIsArray($eventConditions, 'CFGS_Google_GoalEventDetailsEventConditions', __METHOD__);
    $this->eventConditions = $eventConditions;
  }
  public function getEventConditions() {
    return $this->eventConditions;
  }
  public function setUseEventValue($useEventValue) {
    $this->useEventValue = $useEventValue;
  }
  public function getUseEventValue() {
    return $this->useEventValue;
  }
}

class CFGS_Google_GoalEventDetailsEventConditions extends CFGS_Google_Model {
  public $type;
  public $matchType;
  public $expression;
  public $comparisonType;
  public $comparisonValue;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setMatchType($matchType) {
    $this->matchType = $matchType;
  }
  public function getMatchType() {
    return $this->matchType;
  }
  public function setExpression($expression) {
    $this->expression = $expression;
  }
  public function getExpression() {
    return $this->expression;
  }
  public function setComparisonType($comparisonType) {
    $this->comparisonType = $comparisonType;
  }
  public function getComparisonType() {
    return $this->comparisonType;
  }
  public function setComparisonValue($comparisonValue) {
    $this->comparisonValue = $comparisonValue;
  }
  public function getComparisonValue() {
    return $this->comparisonValue;
  }
}

class CFGS_Google_GoalParentLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_GoalUrlDestinationDetails extends CFGS_Google_Model {
  public $url;
  public $caseSensitive;
  public $matchType;
  protected $__stepsType = 'CFGS_Google_GoalUrlDestinationDetailsSteps';
  protected $__stepsDataType = 'array';
  public $steps;
  public $firstStepRequired;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setCaseSensitive($caseSensitive) {
    $this->caseSensitive = $caseSensitive;
  }
  public function getCaseSensitive() {
    return $this->caseSensitive;
  }
  public function setMatchType($matchType) {
    $this->matchType = $matchType;
  }
  public function getMatchType() {
    return $this->matchType;
  }
  public function setSteps(/* array(CFGS_Google_GoalUrlDestinationDetailsSteps) */ $steps) {
    $this->assertIsArray($steps, 'CFGS_Google_GoalUrlDestinationDetailsSteps', __METHOD__);
    $this->steps = $steps;
  }
  public function getSteps() {
    return $this->steps;
  }
  public function setFirstStepRequired($firstStepRequired) {
    $this->firstStepRequired = $firstStepRequired;
  }
  public function getFirstStepRequired() {
    return $this->firstStepRequired;
  }
}

class CFGS_Google_GoalUrlDestinationDetailsSteps extends CFGS_Google_Model {
  public $url;
  public $name;
  public $number;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setNumber($number) {
    $this->number = $number;
  }
  public function getNumber() {
    return $this->number;
  }
}

class CFGS_Google_GoalVisitNumPagesDetails extends CFGS_Google_Model {
  public $comparisonType;
  public $comparisonValue;
  public function setComparisonType($comparisonType) {
    $this->comparisonType = $comparisonType;
  }
  public function getComparisonType() {
    return $this->comparisonType;
  }
  public function setComparisonValue($comparisonValue) {
    $this->comparisonValue = $comparisonValue;
  }
  public function getComparisonValue() {
    return $this->comparisonValue;
  }
}

class CFGS_Google_GoalVisitTimeOnSiteDetails extends CFGS_Google_Model {
  public $comparisonType;
  public $comparisonValue;
  public function setComparisonType($comparisonType) {
    $this->comparisonType = $comparisonType;
  }
  public function getComparisonType() {
    return $this->comparisonType;
  }
  public function setComparisonValue($comparisonValue) {
    $this->comparisonValue = $comparisonValue;
  }
  public function getComparisonValue() {
    return $this->comparisonValue;
  }
}

class CFGS_Google_Goals extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Goal';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_Goal) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Goal', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_McfData extends CFGS_Google_Model {
  public $kind;
  protected $__rowsType = 'CFGS_Google_McfDataRows';
  protected $__rowsDataType = 'array';
  public $rows;
  public $containsSampledData;
  public $totalResults;
  public $itemsPerPage;
  public $totalsForAllResults;
  public $nextLink;
  public $id;
  protected $__queryType = 'CFGS_Google_McfDataQuery';
  protected $__queryDataType = '';
  public $query;
  public $previousLink;
  protected $__profileInfoType = 'CFGS_Google_McfDataProfileInfo';
  protected $__profileInfoDataType = '';
  public $profileInfo;
  protected $__columnHeadersType = 'CFGS_Google_McfDataColumnHeaders';
  protected $__columnHeadersDataType = 'array';
  public $columnHeaders;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows(/* array(CFGS_Google_McfDataRows) */ $rows) {
    $this->assertIsArray($rows, 'CFGS_Google_McfDataRows', __METHOD__);
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setContainsSampledData($containsSampledData) {
    $this->containsSampledData = $containsSampledData;
  }
  public function getContainsSampledData() {
    return $this->containsSampledData;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setTotalsForAllResults($totalsForAllResults) {
    $this->totalsForAllResults = $totalsForAllResults;
  }
  public function getTotalsForAllResults() {
    return $this->totalsForAllResults;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setQuery(CFGS_Google_McfDataQuery $query) {
    $this->query = $query;
  }
  public function getQuery() {
    return $this->query;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setProfileInfo(CFGS_Google_McfDataProfileInfo $profileInfo) {
    $this->profileInfo = $profileInfo;
  }
  public function getProfileInfo() {
    return $this->profileInfo;
  }
  public function setColumnHeaders(/* array(CFGS_Google_McfDataColumnHeaders) */ $columnHeaders) {
    $this->assertIsArray($columnHeaders, 'CFGS_Google_McfDataColumnHeaders', __METHOD__);
    $this->columnHeaders = $columnHeaders;
  }
  public function getColumnHeaders() {
    return $this->columnHeaders;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class CFGS_Google_McfDataColumnHeaders extends CFGS_Google_Model {
  public $dataType;
  public $columnType;
  public $name;
  public function setDataType($dataType) {
    $this->dataType = $dataType;
  }
  public function getDataType() {
    return $this->dataType;
  }
  public function setColumnType($columnType) {
    $this->columnType = $columnType;
  }
  public function getColumnType() {
    return $this->columnType;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_McfDataProfileInfo extends CFGS_Google_Model {
  public $webPropertyId;
  public $internalWebPropertyId;
  public $tableId;
  public $profileId;
  public $profileName;
  public $accountId;
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setTableId($tableId) {
    $this->tableId = $tableId;
  }
  public function getTableId() {
    return $this->tableId;
  }
  public function setProfileId($profileId) {
    $this->profileId = $profileId;
  }
  public function getProfileId() {
    return $this->profileId;
  }
  public function setProfileName($profileName) {
    $this->profileName = $profileName;
  }
  public function getProfileName() {
    return $this->profileName;
  }
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_McfDataQuery extends CFGS_Google_Model {
  public $max_results;
  public $sort;
  public $dimensions;
  public $start_date;
  public $start_index;
  public $segment;
  public $ids;
  public $metrics;
  public $filters;
  public $end_date;
  public function setMax_results($max_results) {
    $this->max_results = $max_results;
  }
  public function getMax_results() {
    return $this->max_results;
  }
  public function setSort($sort) {
    $this->sort = $sort;
  }
  public function getSort() {
    return $this->sort;
  }
  public function setDimensions($dimensions) {
    $this->dimensions = $dimensions;
  }
  public function getDimensions() {
    return $this->dimensions;
  }
  public function setStart_date($start_date) {
    $this->start_date = $start_date;
  }
  public function getStart_date() {
    return $this->start_date;
  }
  public function setStart_index($start_index) {
    $this->start_index = $start_index;
  }
  public function getStart_index() {
    return $this->start_index;
  }
  public function setSegment($segment) {
    $this->segment = $segment;
  }
  public function getSegment() {
    return $this->segment;
  }
  public function setIds($ids) {
    $this->ids = $ids;
  }
  public function getIds() {
    return $this->ids;
  }
  public function setMetrics($metrics) {
    $this->metrics = $metrics;
  }
  public function getMetrics() {
    return $this->metrics;
  }
  public function setFilters($filters) {
    $this->filters = $filters;
  }
  public function getFilters() {
    return $this->filters;
  }
  public function setEnd_date($end_date) {
    $this->end_date = $end_date;
  }
  public function getEnd_date() {
    return $this->end_date;
  }
}

class CFGS_Google_McfDataRows extends CFGS_Google_Model {
  public $primitiveValue;
  protected $__conversionPathValueType = 'CFGS_Google_McfDataRowsConversionPathValue';
  protected $__conversionPathValueDataType = 'array';
  public $conversionPathValue;
  public function setPrimitiveValue($primitiveValue) {
    $this->primitiveValue = $primitiveValue;
  }
  public function getPrimitiveValue() {
    return $this->primitiveValue;
  }
  public function setConversionPathValue(/* array(CFGS_Google_McfDataRowsConversionPathValue) */ $conversionPathValue) {
    $this->assertIsArray($conversionPathValue, 'CFGS_Google_McfDataRowsConversionPathValue', __METHOD__);
    $this->conversionPathValue = $conversionPathValue;
  }
  public function getConversionPathValue() {
    return $this->conversionPathValue;
  }
}

class CFGS_Google_McfDataRowsConversionPathValue extends CFGS_Google_Model {
  public $nodeValue;
  public $interactionType;
  public function setNodeValue($nodeValue) {
    $this->nodeValue = $nodeValue;
  }
  public function getNodeValue() {
    return $this->nodeValue;
  }
  public function setInteractionType($interactionType) {
    $this->interactionType = $interactionType;
  }
  public function getInteractionType() {
    return $this->interactionType;
  }
}

class CFGS_Google_Profile extends CFGS_Google_Model {
  public $defaultPage;
  public $kind;
  public $excludeQueryParameters;
  public $name;
  public $created;
  public $webPropertyId;
  public $updated;
  public $siteSearchQueryParameters;
  public $websiteUrl;
  public $currency;
  public $internalWebPropertyId;
  protected $__childLinkType = 'CFGS_Google_ProfileChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  public $eCommerceTracking;
  public $timezone;
  public $siteSearchCategoryParameters;
  protected $__parentLinkType = 'CFGS_Google_ProfileParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $id;
  public $selfLink;
  public $accountId;
  public function setDefaultPage($defaultPage) {
    $this->defaultPage = $defaultPage;
  }
  public function getDefaultPage() {
    return $this->defaultPage;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setExcludeQueryParameters($excludeQueryParameters) {
    $this->excludeQueryParameters = $excludeQueryParameters;
  }
  public function getExcludeQueryParameters() {
    return $this->excludeQueryParameters;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setWebPropertyId($webPropertyId) {
    $this->webPropertyId = $webPropertyId;
  }
  public function getWebPropertyId() {
    return $this->webPropertyId;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setSiteSearchQueryParameters($siteSearchQueryParameters) {
    $this->siteSearchQueryParameters = $siteSearchQueryParameters;
  }
  public function getSiteSearchQueryParameters() {
    return $this->siteSearchQueryParameters;
  }
  public function setWebsiteUrl($websiteUrl) {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl() {
    return $this->websiteUrl;
  }
  public function setCurrency($currency) {
    $this->currency = $currency;
  }
  public function getCurrency() {
    return $this->currency;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setChildLink(CFGS_Google_ProfileChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setECommerceTracking($eCommerceTracking) {
    $this->eCommerceTracking = $eCommerceTracking;
  }
  public function getECommerceTracking() {
    return $this->eCommerceTracking;
  }
  public function setTimezone($timezone) {
    $this->timezone = $timezone;
  }
  public function getTimezone() {
    return $this->timezone;
  }
  public function setSiteSearchCategoryParameters($siteSearchCategoryParameters) {
    $this->siteSearchCategoryParameters = $siteSearchCategoryParameters;
  }
  public function getSiteSearchCategoryParameters() {
    return $this->siteSearchCategoryParameters;
  }
  public function setParentLink(CFGS_Google_ProfileParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
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
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_ProfileChildLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_ProfileParentLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_Profiles extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Profile';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_Profile) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Profile', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_Segment extends CFGS_Google_Model {
  public $definition;
  public $kind;
  public $segmentId;
  public $created;
  public $updated;
  public $id;
  public $selfLink;
  public $name;
  public function setDefinition($definition) {
    $this->definition = $definition;
  }
  public function getDefinition() {
    return $this->definition;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setSegmentId($segmentId) {
    $this->segmentId = $segmentId;
  }
  public function getSegmentId() {
    return $this->segmentId;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
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
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class CFGS_Google_Segments extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Segment';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_Segment) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Segment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_Webproperties extends CFGS_Google_Model {
  public $username;
  public $kind;
  protected $__itemsType = 'CFGS_Google_Webproperty';
  protected $__itemsDataType = 'array';
  public $items;
  public $itemsPerPage;
  public $previousLink;
  public $startIndex;
  public $nextLink;
  public $totalResults;
  public function setUsername($username) {
    $this->username = $username;
  }
  public function getUsername() {
    return $this->username;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(CFGS_Google_Webproperty) */ $items) {
    $this->assertIsArray($items, 'CFGS_Google_Webproperty', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }
  public function getItemsPerPage() {
    return $this->itemsPerPage;
  }
  public function setPreviousLink($previousLink) {
    $this->previousLink = $previousLink;
  }
  public function getPreviousLink() {
    return $this->previousLink;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
}

class CFGS_Google_Webproperty extends CFGS_Google_Model {
  public $kind;
  public $name;
  public $created;
  public $updated;
  public $websiteUrl;
  public $internalWebPropertyId;
  protected $__childLinkType = 'CFGS_Google_WebpropertyChildLink';
  protected $__childLinkDataType = '';
  public $childLink;
  protected $__parentLinkType = 'CFGS_Google_WebpropertyParentLink';
  protected $__parentLinkDataType = '';
  public $parentLink;
  public $id;
  public $selfLink;
  public $accountId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setWebsiteUrl($websiteUrl) {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl() {
    return $this->websiteUrl;
  }
  public function setInternalWebPropertyId($internalWebPropertyId) {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  public function getInternalWebPropertyId() {
    return $this->internalWebPropertyId;
  }
  public function setChildLink(CFGS_Google_WebpropertyChildLink $childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setParentLink(CFGS_Google_WebpropertyParentLink $parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
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
  public function setAccountId($accountId) {
    $this->accountId = $accountId;
  }
  public function getAccountId() {
    return $this->accountId;
  }
}

class CFGS_Google_WebpropertyChildLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}

class CFGS_Google_WebpropertyParentLink extends CFGS_Google_Model {
  public $href;
  public $type;
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
}
