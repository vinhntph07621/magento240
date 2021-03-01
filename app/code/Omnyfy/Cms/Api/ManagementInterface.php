<?php
namespace Omnyfy\Cms\Api;

interface ManagementInterface
{
    /**
     * Create new item.
     *
     * @api
     * @param string $data.
     * @return string.
     */
    public function create($data);

    /**
     * Update item by id.
     *
     * @api
     * @param int $id.
     * @param string $data.
     * @return string.
     */
    public function update($id, $data);

    /**
     * Remove item by id.
     *
     * @api
     * @param int $id.
     * @return bool.
     */
    public function delete($id);
}