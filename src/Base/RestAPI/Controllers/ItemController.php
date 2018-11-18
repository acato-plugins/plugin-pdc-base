<?php
/**
 * Controller which handles the (requested) pdc-item(s).
 */

namespace OWC\PDC\Base\RestAPI\Controllers;

use OWC\PDC\Base\Models\Item;
use WP_Error;
use WP_REST_Request;

/**
 * Controller which handles the (requested) pdc-item(s).
 */
class ItemController extends BaseController
{

    /**
     * Get a list of all items.
     *
     * @param WP_REST_Request $request
     *
     * @return array
     */
    public function getItems(WP_REST_Request $request)
    {
        $parameters = $this->convertParameters($request->get_params());
        $items      = (new Item())
            ->hide(['connected'])
            ->query(apply_filters('owc/pdc/rest-api/items/query', $this->getPaginatorParams($request)))
            ->query($parameters)
            ->query($this->hideInactiveItem());

        $data  = $items->all();
        $query = $items->getQuery();

        return $this->addPaginator($data, $query);
    }

    /**
     * Convert the parameters to the allowed ones.
     *
     * @param array $parametersFromRequest
     * @return array
     */
    protected function convertParameters(array $parametersFromRequest): array
    {
        $parameters = [];

        if (isset($parametersFromRequest['name'])) {
            $parameters['name'] = esc_attr($parametersFromRequest['name']);
        }

        if (isset($parametersFromRequest['slug'])) {
            $parameters['name'] = esc_attr($parametersFromRequest['slug']);
            unset($parametersFromRequest['slug']);
        }

        if (isset($parametersFromRequest['id'])) {
            $parameters['p'] = absint($parametersFromRequest['id']);
            unset($parametersFromRequest['slug']);
        }

        return $parameters;
    }

    /**
     * Get an individual post item.
     *
     * @param $request $request
     *
     * @return array|WP_Error
     */
    public function getItem(WP_REST_Request $request)
    {
        $id = (int) $request->get_param('id');

        $item = (new Item)
            ->query(apply_filters('owc/pdc/rest-api/items/query/single', []))
            ->query($this->hideInactiveItem())
            ->find($id);

        if (!$item) {
            return new WP_Error('no_item_found', sprintf('Item with ID "%d" not found', $id), [
                'status' => 404,
            ]);
        }

        return $item;
    }

    /**
     * Hide inactive item from output.
     *
     * @return array
     */
    protected function hideInactiveItem()
    {
        return [
            'meta_query' => [
                [
                    'key'   => '_owc_pdc_active',
                    'value' => 1,
                ],
            ],
        ];
    }
}
