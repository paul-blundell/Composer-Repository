<?php
require_once('vendor/autoload.php');

/**
 * This class will use the BitBucket API
 * to gather a list of repositories before then generating
 * the JSON file required for the Satis build.
 *
 * @author Paul Blundell <paul@blundell.xyz>
 */
class SatisBuild
{
    /**
     * The BitBucket OAuth keys for the
     * your team
     */
    const OAUTH_KEY = '';
    const OAUTH_SECRET = '';

    /**
     * The URL to BitBucket
     */
    const BITBUCKET = 'git@composer:';

    /**
     * The name and URL of the composer repository
     */
    const NAME = 'Composer Repository';
    const URL = '';

    /**
     * The output directory
     * @var string
     */
    private $output;

    /**
     * Constructor
     * @param string $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * Start the build
     */
    public function run()
    {
        // Gather a list of repositories
        $repositories = $this->getRepositories();

        // Generate the JSON file
        $this->generateSatisJson($repositories);

        // Run the Satis build script
        $this->buildComposer();
    }

    /**
     * Execute the main Satis build script
     */
    private function buildComposer()
    {
        exec(sprintf('php vendor/bin/satis build satis.json %s -n', $this->output));
    }

    /**
     * Get a list of repositories from the BitBucket API
     * @return array
     */
    private function getRepositories()
    {
        $oauth_params = array(
            'oauth_consumer_key'      => self::OAUTH_KEY,
            'oauth_consumer_secret'   => self::OAUTH_SECRET
        );

        $user = new Bitbucket\API\User;
        $user->getClient()->addListener(
              new Bitbucket\API\Http\Listener\OAuthListener($oauth_params)
        );

        $response = $user->get();
        $content = json_decode($response->getContent());

        return $content->repositories;
    }

    /**
     * Generate the Satis JSON file
     * @param type $repositories
     * @return string
     */
    private function generateSatisJson($repositories)
    {
        $result = [
            'name' => self::NAME,
            'homepage' => self::URL,
            'repositories' => [],
            'require-all' => true
        ];

        foreach ($repositories AS $repo)
        {
            if ($repo->slug === 'repository')
                continue;

            $result['repositories'][] = [
                'type' => 'vcs',
                'url' => self::BITBUCKET.$repo->owner.'/'.$repo->slug.'.git'
            ];
        }

        file_put_contents('satis.json', json_encode($result));
    }

}

$path = isset($argv[1]) ? $argv[1] : 'htdocs';

$satis = new SatisBuild($path);
$satis->run();
