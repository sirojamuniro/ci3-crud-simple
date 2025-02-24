<?php

defined('BASEPATH') or exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

class Product extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->output->set_content_type('application/json');
    }

    // Get all products with pagination
    public function index()
    {
        $limit = $this->input->get('limit') ?: 10;
        $offset = $this->input->get('offset') ?: 0;

        $products = $this->Product_model->get_all($limit, $offset);
        $total = $this->Product_model->count_all();

        $this->output->set_output(json_encode([
            'data' => $products,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]));
    }

    // Get single product by ID
    public function show($id)
    {
        $product = $this->Product_model->get_by_id($id);
        if ($product) {
            $this->output->set_output(json_encode($product));
        } else {
            $this->output->set_status_header(404)->set_output(json_encode(['message' => 'Product not found']));
        }
    }

    // Create new product
    public function store()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        // Validation
        if (empty($data['name'])) {
            $this->output->set_status_header(400)->set_output(json_encode(['message' => 'Name is required']));
            return;
        }
        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $this->output->set_status_header(400)->set_output(json_encode(['message' => 'Price must be a number greater than 0']));
            return;
        }

        if ($this->Product_model->insert($data)) {
            $this->output->set_status_header(201)->set_output(json_encode(['message' => 'Product created']));
        } else {
            $this->output->set_status_header(500)->set_output(json_encode(['message' => 'Failed to create product']));
        }
    }

    // Update product
    public function update($id)
    {
        $data = json_decode($this->input->raw_input_stream, true);

        // Validation
        if (isset($data['name']) && empty($data['name'])) {
            $this->output->set_status_header(400)->set_output(json_encode(['message' => 'Name is required']));
            return;
        }
        if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] <= 0)) {
            $this->output->set_status_header(400)->set_output(json_encode(['message' => 'Price must be a number greater than 0']));
            return;
        }

        if ($this->Product_model->update($id, $data)) {
            $this->output->set_output(json_encode(['message' => 'Product updated']));
        } else {
            $this->output->set_status_header(500)->set_output(json_encode(['message' => 'Failed to update product']));
        }
    }

    // Delete product
    public function delete($id)
    {
        if ($this->Product_model->delete($id)) {
            $this->output->set_output(json_encode(['message' => 'Product deleted']));
        } else {
            $this->output->set_status_header(500)->set_output(json_encode(['message' => 'Failed to delete product']));
        }
    }
}
