<?php
/**
 * Class DBConnection:
 * This class handles the database connection using the mysqli extension in PHP.
 * It encapsulates the logic for connecting to and disconnecting from the database within an object-oriented structure.
 */
class DBConnection {
    /**
     * Protected property $db:
     * This property holds the mysqli object, representing the database connection.
     */
    protected $db;

    /**
     * Constructor of the DBConnection class:
     * The constructor initializes the database connection using the mysqli constructor.
     * It checks if the connection was successful and terminates the script with an error message if it fails.
     */
    function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'julies_db');
        if (!$this->db) {
            die('Database Connection Failed. Error: ' . $this->db->error);
        }
    }

    /**
     * Method db_connect:
     * This method returns the mysqli database connection object.
     * It provides access to the database connection for other parts of the application.
     */
    function db_connect() {
        return $this->db;
    }

    /**
     * Destructor of the DBConnection class:
     * The destructor closes the database connection when an instance of DBConnection is destroyed.
     * This ensures that the database connection is properly closed.
     */
    function __destruct() {
        $this->db->close();
    }
}

/**
 * Function format_num:
 * This function formats a numeric value to a specified number of decimal places.
 * It checks if the input is numeric and formats it accordingly, returning 'Invalid input.' if the input is not numeric.
 */
function format_num($number = '', $decimal = '') {
    if (is_numeric($number)) {
        $ex = explode(".", $number);
        $dec_len = isset($ex[1]) ? strlen($ex[1]) : 0;
        if (!empty($decimal) || is_numeric($decimal)) {
            return number_format($number, $decimal);
        } else {
            return number_format($number, $dec_len);
        }
    } else {
        return 'Invalid input.';
    }
}

/**
 * Creating an instance of DBConnection:
 * This creates a new DBConnection object, which initializes the database connection.
 */
$db = new DBConnection();

/**
 * Assigning the database connection to $conn:
 * This assigns the mysqli database connection object to the $conn variable.
 * It allows the use of the $conn variable to interact with the database.
 */
$conn = $db->db_connect();
?>