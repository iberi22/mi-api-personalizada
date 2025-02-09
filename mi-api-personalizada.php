<?php
/**
 * Plugin Name: Mi API Personalizada
 * Plugin URI: https://github.com/iberi22/mi-api-personalizada
 * Description: Crea una API personalizada para obtener datos de posts.
 * Version: 1.0.0
 * Author: Brahyan Belalcazar theberi
 * Author URI: https://github.com/iberi22
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_action('rest_api_init', 'registrar_endpoint_posts');

function registrar_endpoint_posts() {
    register_rest_route(
        'mi-api/v1', // Namespace de tu API (puedes cambiarlo)
        '/blogs',   // Ruta del endpoint (puedes cambiarlo)
        array(
            'methods'  => 'GET', // Método HTTP (GET para obtener datos)
            'callback' => 'obtener_blogsts_personalizados', // Función que procesará la solicitud
        )
    );
}

function obtener_blogsts_personalizados() {
    $posts = get_posts(array(
        'numberposts' => -1, // Obtener todos los posts (-1)
        'post_status' => 'publish', // Solo posts publicados
    ));

    $data = array(); // Array para almacenar los datos formateados

    foreach ($posts as $post) {
        $featured_image_id = get_post_thumbnail_id($post->ID);
        $featured_image_url = wp_get_attachment_url($featured_image_id);

        // Recuperar la metadata de la imagen
        $metadata = wp_get_attachment_metadata($featured_image_id);

        if ( isset($metadata['sizes']['medium']) ) {
            // Obtener la información del directorio de uploads
            $upload_dir = wp_upload_dir();
            $medium = $metadata['sizes']['medium'];
            // Construir la URL completa para la imagen de tamaño medium
            $medium['source_url'] = trailingslashit($upload_dir['baseurl']) . $medium['file'];
        } else {
            $medium = null;
        }

        $etiquetas = wp_get_post_tags($post->ID); // Obtener las etiquetas del post
        $etiquetas_array = array();
        if ($etiquetas) {
            foreach ($etiquetas as $etiqueta) {
                $etiquetas_array[] = array(
                    'id' => $etiqueta->term_id,
                    'name' => $etiqueta->name,
                    'slug' => $etiqueta->slug,
                );
            }
        }

        $data[] = array(
            'id'          => $post->ID,
            'title'       => $post->post_title,
            'date'        => get_the_date('Y-m-d H:i:s', $post->ID), // Formato de fecha
            'image'       => $featured_image_url,
            'image_medium'=> $medium, // Datos del tamaño medium
            'content'     => apply_filters('the_content', $post->post_content), // Aplica filtros de contenido (para shortcodes, etc.)
            'tags'        => $etiquetas_array,
            'link'        => get_permalink($post->ID)  // Opcional: Enlace al post
        );
    }

    return rest_ensure_response($data); // Convierte los datos en una respuesta REST válida
}