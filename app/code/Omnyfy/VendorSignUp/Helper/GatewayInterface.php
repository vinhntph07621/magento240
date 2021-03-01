<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-17
 * Time: 12:33
 */
namespace Omnyfy\VendorSignUp\Helper;

interface GatewayInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function createUser($data);

    /**
     * @param array $data
     * @param string $userId
     * @return array
     */
    public function updateUser($data, $userId);

    /**
     * @param array $data
     * @return array
     */
    public function createCompany($data);

    /**
     * @param array $data
     * @return array
     */
    public function updateCompany($data);

    /**
     * @param array $data
     * @return array
     */
    public function createBankAccount($data);

    /**
     * @param int $userId
     * @return array
     */
    public function getUserById($userId);

    /**
     * @param string $url
     * @return array
     * @deprecated Use specified method instead
     */
    public function getAssemblyDetails($url);

    /**
     * @param string $email
     * @return array
     */
    public function searchUser($email);

    /**
     * @param string $email
     * @return boolean
     */
    public function isUserExist($email);

    /**
     * @param string $userId
     * @return array
     */
    public function getBankAccountByUserId($userId);

    /**
     * @param string $userId
     * @return mixed
     */
    public function getWalletByUserId($userId);

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readBankAccountId($data);

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readUserId($data);

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readUserStatus($data);

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readCompanyId($data);

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readWalletId($data);
}
 