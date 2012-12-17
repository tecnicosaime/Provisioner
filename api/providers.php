<?php 

class Providers {
    public $db;

    private $_FIELDS = array('name', 'settings', 'domain', 'default_account_id');

    function __construct() {
        $this->db = new BigCouch(DB_SERVER);
    }

    /**
     *  This is the function that will allow the administrator to retrieve all the providers
     *
     * @url GET /
     */
    function retrieveAll() {
        return $this->db->getAllByKey('providers', 'domain');
    }

    // This will get the information for a specific provider
    function get($provider_id) {
        $provider = $this->db->get('providers', $provider_id);

        if ($provider)
            return $provider;
        else
            throw new RestException(200, 'No information for this provider');
    }

    // This will update the information for a provider
    function post($provider_id, $request_data = null) {
        Validator::validateEdit($request_data, $this->_FIELDS);

        foreach ($request_data as $key => $value) {
            if (!$this->db->update('providers', $provider_id, $key, $value))
                throw new RestException(500, 'Error while saving');
        }

        return array('status' => true, 'message' => 'Provider successfully modified');
    }

    // This will add a provider
    function put($request_data = null) {
        $object_ready = $this->db->prepareAddProviders($request_data);
        if (!$this->db->add('providers', Validator::validateAdd($object_ready, $this->_FIELDS)))
            throw new RestException(500, 'Error while Adding');
        else
            return array('status' => true, 'message' => 'Provider successfully added');
    }

    // This function only delete the provider
    // TODO: allow the provider to also delete the users linked to this provider.
    function delete($provider_id) {
        if (!$this->db->delete('providers', $provider_id))
            throw new RestException(500, 'Error while deleting');
        else
            return array('status' => true, 'message' => 'Provider successfully deleted');
    }
}

?>