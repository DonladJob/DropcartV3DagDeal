<?php
/**
 * =========================================================
 * DROPCART
 * =========================================================
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * ---------------------------------------------------------
 *
 * File: shopping_cart.php
 * Date: 27-02-18 12:00
 *
 * @author Dropcart <info@dropcart.nl>
 * @copyright © [2016 - 2018] Dropcart - All rights reserved.
 * @license MIT (https://opensource.org/licenses/MIT)
 * @version 3.0.0
 *
 * =========================================================
 */

class shopping_cart {

    /**
     * @var array $shopping_cart
     */
    private $shopping_cart;

    /**
     * @var array $config
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        if (!isset($_SESSION['shopping_cart'])) {
            $_SESSION['shopping_cart'] = [];
        }

        $this->shopping_cart = $_SESSION['shopping_cart'];
    }

    /**
     * Adds a product and the desired quantity to the shopping_cart in the session.
     *
     * @param $product_id
     * @param $quantity
     */
    public function addProduct($product_id, $quantity)
    {
        if (!key_exists($product_id, $_SESSION['shopping_cart'])) {
            $_SESSION['shopping_cart'][$product_id] = [
                'product'     => request($this->config, 'catalog', 'products', $product_id),
                'quantity'    => $quantity,
            ];
        } else {
            $_SESSION['shopping_cart'][$product_id]['quantity'] += $quantity;
        }

        $this->shopping_cart = $_SESSION['shopping_cart'];
    }

    /**
     * Removes a product from the shopping_cart with the provided quantity.
     * If no quantity is provided the product will be completely removed from the shopping_cart.
     *
     * @param $product_id
     * @param int|null $quantity
     */
    public function removeProduct($product_id, $quantity = null)
    {
        if ($quantity) {
            if ($_SESSION['shopping_cart'][$product_id]['quantity'] <= 1) {
                unset($_SESSION['shopping_cart'][$product_id]);
            } else {
                $_SESSION['shopping_cart'][$product_id]['quantity'] -= 1;
            }
        } else {
            unset($_SESSION['shopping_cart'][$product_id]);
        }

        $this->shopping_cart = $_SESSION['shopping_cart'];
    }

    /**
     * Returns the shopping_cart.
     *
     * @return array
     */
    public function get()
    {
        return $this->shopping_cart;
    }

    /**
     * Returns the shopping_cart overview with a sum of the total quantities and prices.
     *
     * @return array
     */
    public function overview()
    {
        $overview = [];

        $overview['total_products'] = 0;
        $overview['total_price']    = 0;

        foreach ($this->shopping_cart as $order_product)
        {
            $overview['total_products'] += $order_product['quantity'];
            $overview['total_price']    += ($order_product['product']->price_details->piece_price->in * $order_product['quantity']);
        }

        return $overview;
    }

}