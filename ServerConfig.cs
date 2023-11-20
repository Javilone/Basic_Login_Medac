using System.Collections;
using System.Collections.Generic;
using Unity.VisualScripting;
using UnityEngine.Events;
using UnityEngine;
using UnityEngine.Networking;

// Script usado para lo relacionado con servidor. 

[CreateAssetMenu(fileName = "Servidor", menuName = "JServidor")]

public class ServerConfig : ScriptableObject
{
    public string servidor;
    public Servicios[] servicio;

    public bool isBusy = false;
    public Respuesta respuesta;


    // Llamada a un servicio que está en "Internet".
    public IEnumerator LlamarServicio(string nombre, string[] datos, UnityAction e)
    {
        isBusy = true;  // isBusy: indica si la función está ocupada realizando alguna tarea
        WWWForm formulario = new WWWForm();
        Servicios s = new Servicios();


        /* Se obtiene el nombre del servicio recorriendo la lista entera de servicios
         Se utiliza para encontrar el servicio correspondiente al nombre pasado como argumento */
        for (int i = 0; i < servicio.Length; i++)
        {
            if (servicio[i].nombre.Equals(nombre))
            {
                s = servicio[i];
            }
        }
        /* Se utiliza para agregar campos y valores al objeto form con el mét0do AddField.
           Estos campos y valores se utilizan para construir los datos que se enviarán al servicio web.*/
        for (int i = 0; i < s.parametros.Length; i++)
        {
            formulario.AddField(s.parametros[i], datos[i]);
        }

        // Petición de URL al servidor
        UnityWebRequest www = UnityWebRequest.Post(servidor + "/" + s.URL, formulario);
        yield return www.SendWebRequest();

        /* Verifica si la solicitud HTTP se completó con éxito
         Si se completó con éxito, analiza la respuesta JSON del servidor y la almacena en un objeto
         de la clase Respuesta. */
        if (www.result != UnityWebRequest.Result.Success)
        {
            respuesta = new Respuesta();  //Llamada al constructor en caso de no lograr la llamada
        }
        else
        {

            Debug.Log(respuesta);
            Debug.Log(respuesta.respuesta);
            respuesta = JsonUtility.FromJson<Respuesta>(www.downloadHandler.text);

        }
        isBusy = false;
        e.Invoke();
    }
}


[System.Serializable]  // Esto hará que en el "Script" Servidor aparezcan las variables como parámetros
public class Servicios
{
    public string nombre;
    public string URL;
    public string[] parametros;
}


[System.Serializable]  // Esto hará que en el script Servidor aparezcan como parámetros
public class Respuesta
{
    public int codigo;
    public string mensaje;
    public string respuesta;  // en respuesta es donde estará almacenado desde php el json del usuario
    public int id;
    public string rol;
    public string usuario;
    public string nombre;
    public string apellido1;
    public string apellido2;
    

    public Respuesta() // Respuesta generica en caso de no lograr la conexion 
    {
        codigo = 404;
        mensaje = "Error";
     
    }
}


[System.Serializable]
public class DDBBUsuarios // Donde se almacenarán los datos del json usuario.
{
    public int id;
    public string usuario;
    public string rol;
    public string nombre;
    public string apellido1;
    public string apellido2;
}