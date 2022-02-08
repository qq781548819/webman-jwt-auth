<?php
declare(strict_types=1);

namespace Nyuwa\Jwt;

use Psr\Container\ContainerInterface;

abstract class AbstractJWT implements JWTInterface
{
    /**
     * @var string
     */
    public $tokenPrefix = 'Bearer';

    public $tokenScenePrefix = 'jwt_scene';

    /**
     * @var array Supported algorithms
     */
    private $supportedAlgs = [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
        'ES256' => 'Lcobucci\JWT\Signer\Ecdsa\Sha256',
        'ES384' => 'Lcobucci\JWT\Signer\Ecdsa\Sha384',
        'ES512' => 'Lcobucci\JWT\Signer\Ecdsa\Sha512',
        'RS256' => 'Lcobucci\JWT\Signer\Rsa\Sha256',
        'RS384' => 'Lcobucci\JWT\Signer\Rsa\Sha384',
        'RS512' => 'Lcobucci\JWT\Signer\Rsa\Sha512',
    ];

    // 对称算法名称
    private $symmetryAlgs = [
        'HS256',
        'HS384',
        'HS512'
    ];

    // 非对称算法名称
    private $asymmetricAlgs = [
        'RS256',
        'RS384',
        'RS512',
        'ES256',
        'ES384',
        'ES512',
    ];

    /**
     * 当前token生成token的场景值
     * @var string
     */
    private $scene = 'default';

    /**
     * @var string
     */
    private $scenePrefix = 'scene';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $config;


    /**
     * jwt配置前缀
     * @var string
     */
    private $configPrefix = 'plugin.nyuwa.jwt.app';

    public function __construct()
    {
        $this->config = config("plugin.nyuwa.jwt.app",[]);
        $config = $this->config;
        if (empty($config['supported_algs'])) $config['supported_algs'] = $this->supportedAlgs;
        if (empty($config['symmetry_algs'])) $config['symmetry_algs'] = $this->symmetryAlgs;
        if (empty($config['asymmetric_algs'])) $config['asymmetric_algs'] = $this->asymmetricAlgs;
        if (empty($config['blacklist_prefix'])) $config['blacklist_prefix'] = 'webman_admin_jwt';
        $scenes = $config['scene'];
        unset($config['scene']);
        foreach ($scenes as $key => $scene) {
            $sceneConfig = array_merge($config, $scene);
            $this->setSceneConfig($key, $sceneConfig);
        }
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * 设置场景值
     * @param string $scene
     */
    public function setScene(string $scene)
    {
        $this->scene = $scene;
        return $this;
    }

    /**
     * 获取当前场景值
     * @return string
     */
    public function getScene()
    {
        return $this->scene;
    }

    /**
     * @param string $scene
     * @param null   $value
     * @return $this
     */
    public function setSceneConfig(string $scene = 'default', $value = null)
    {
        $this->config["{$this->configPrefix}.{$this->scenePrefix}.{$scene}"] = $value;
        return $this;
    }

    /**
     * @param string $scene
     * @return mixed
     */
    public function getSceneConfig(string $scene = 'default')
    {
        return $this->config["{$this->configPrefix}.{$this->scenePrefix}.{$scene}"];
    }
}