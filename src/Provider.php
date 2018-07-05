<?php

namespace CauseLabs\SocialiteProviders\IDEO;

use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'IDEO';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [''];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://profile.ideo.com/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://profile.ideo.com/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://profile.ideo.com/api/v1/users/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $location = isset($user['included'][0])
            && isset($user['included'][0]['type'])
            && $user['included'][0]['type'] === 'locations'
                ? $user['included'][0]['attributes']
                : null;

        return (new User())->setRaw($user)->map([
            'id'              => $user['data']['id'],
            'nickname'        => $user['data']['attributes']['username'],
            'name'            => $user['data']['attributes']['first_name'] . ' ' . $user['data']['attributes']['last_name'],
            'first_name'      => $user['data']['attributes']['first_name'],
            'last_name'       => $user['data']['attributes']['last_name'],
            'email'           => $user['data']['attributes']['email'],
            'avatar'          => $user['data']['attributes']['picture'],
            'timezone'        => $user['data']['attributes']['time_zone'],
            'timezone_offset' => $user['data']['attributes']['time_zone_offset'],
            'linkedin_url'    => $user['data']['attributes']['linkedin_url'],
            'location'        => $location,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }
}
