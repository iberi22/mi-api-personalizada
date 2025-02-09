# Documentación de la API y Guía de Uso (Plugin de WordPress)

Este documento describe cómo extender la API con nuevos endpoints y proporciona instrucciones sobre cómo instalarla y usarla como un plugin de WordPress.

## Agregando Nuevos Endpoints

Para agregar un nuevo endpoint a la API, siga estos pasos:

1.  **Definir la Ruta:** Determine el método HTTP (GET, POST, PUT, DELETE, etc.) y la ruta URL para el nuevo endpoint. Elija una ruta que sea lógica y coherente con los endpoints existentes.

2.  **Implementar la Lógica:** Escriba el código para manejar las solicitudes al nuevo endpoint. Esto típicamente implica:
    *   Recibir y validar los datos de entrada (si aplica).
    *   Realizar la lógica de negocio necesaria (por ejemplo, consultar una base de datos, procesar datos).
    *   Retornar una respuesta en un formato adecuado (por ejemplo, JSON).

3.  **Manejo de Errores:** Implemente un manejo de errores adecuado para manejar situaciones inesperadas con elegancia. Retorne códigos de estado HTTP apropiados y mensajes de error informativos.

4.  **Documentación:** Documente el nuevo endpoint en la documentación de la API (este archivo README). Incluya la siguiente información:
    *   URL del endpoint
    *   Método HTTP
    *   Parámetros de solicitud (si los hay)
    *   Formato del cuerpo de la solicitud (si aplica)
    *   Formato de la respuesta
    *   Ejemplos de solicitudes y respuestas

5.  **Pruebas:** Pruebe exhaustivamente el nuevo endpoint para asegurarse de que funciona correctamente y maneja los casos extremos.

**Ejemplo (Conceptual)**

Digamos que quiere agregar un endpoint para recuperar un usuario específico por ID:

*   **Ruta:** `GET /users/:id`
*   **Lógica:**
    *   Extraer el `id` de la URL.
    *   Consultar la base de datos para el usuario con ese ID.
    *   Si el usuario existe, retornar los datos del usuario como JSON.
    *   Si el usuario no existe, retornar un error 404 Not Found.
*   **Documentación:** Agregue una entrada a la documentación de la API explicando el endpoint.

## Instalación (Plugin de WordPress)

1.  **Descargue el Plugin:** Descargue el archivo ZIP del plugin.
2.  **Instale el Plugin:** En el panel de administración de WordPress, vaya a "Plugins" -> "Añadir Nuevo" -> "Subir Plugin". Seleccione el archivo ZIP descargado y haga clic en "Instalar ahora".
3.  **Active el Plugin:** Una vez instalado, haga clic en "Activar Plugin" para activar el plugin.
4.  **Configuración:** Configure cualquier ajuste necesario del plugin en la página de configuración del plugin (si corresponde).
5.  **Verificación:** Verifique que los endpoints de la API estén accesibles.

## Consumiendo la API desde una Aplicación React

Esta sección explica cómo consumir la API desde una aplicación React usando `fetch` o una biblioteca como `axios`.

1.  **Instale un Cliente HTTP (Opcional):** `npm install axios` o `yarn add axios` (Si prefiere `fetch`, omita este paso).

2.  **Componente de Ejemplo:**
```jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios'; // Or use fetch

function UserProfile({ userId }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      try {
        const result = await axios(`/wp-json/mi-plugin/v1/users/${userId}`); // Replace with your API endpoint
        // Or use fetch:
        // const response = await fetch(`/wp-json/mi-plugin/v1/users/${userId}`);
        // const result = await response.json();

        setUser(result.data); //Assuming the API returns {data: {user data}}
      } catch (error) {
        setError(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [userId]);

  if (loading) {
    return <p>Loading user data...</p>;
  }

  if (error) {
    return <p>Error: {error.message}</p>;
  }

  if (!user) {
    return <p>User not found.</p>;
  }

  return (
    <div>
      <h2>User Profile</h2>
      <p>Name: {user.name}</p>
      <p>Email: {user.email}</p>
      {/* Add other user details */}
    </div>
  );
}

export default UserProfile;
