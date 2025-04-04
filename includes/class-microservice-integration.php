<?php
class Microservice_Integration {

    // Fetch recommendations from the external microservice
    public function fetch_recommendations($genre) {
        $api_url = "https://example.com/api/recommendations?genre=" . urlencode($genre);
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return 'Error fetching recommendations';
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['recommendations'])) {
            return $data['recommendations'];
        }

        return 'No recommendations available';
    }
}
