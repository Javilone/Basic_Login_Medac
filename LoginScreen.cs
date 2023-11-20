using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.SceneManagement;
using UnityEngine.UI;
using TMPro;


public class loginScreen : MonoBehaviour
{
    public ServerConfig servidor;
    public TMP_InputField inputUsuario;
    public TMP_InputField inputPassword;
    public GameObject loadingScreen;
    public DDBBUsuarios usuario;
    public TMP_Text userInfo;
    public GameObject canvasLoginManager;

    public void IniciarSesion()
    {
        StartCoroutine(Iniciar());
    }

    IEnumerator Iniciar()
    {
        loadingScreen.SetActive(true);
        string[] datos = new string[2];
        datos[0] = inputUsuario.text;
        datos[1] = inputPassword.text;
        StartCoroutine(servidor.LlamarServicio("login", datos, CargarEscena));
        yield return new WaitForSeconds(0.5f);
        yield return new WaitUntil(() => !servidor.isBusy);

        if (loadingScreen != null) { 
        loadingScreen.SetActive(false);
        }

    }

    // El siguiente met0do se carga después  de consumir el servicio llamado arriba
    void CargarEscena()
    {
        switch (servidor.respuesta.codigo)
        {
            case 204: // Usuario o contraseña incorrectos
                print(servidor.respuesta.mensaje);
                break;
            case 205: // Inicio de sesión correcto
                DontDestroyOnLoad(this.gameObject);
                canvasLoginManager.SetActive(true);
                SceneManager.LoadScene("entradaHospital");

                usuario.id = servidor.respuesta.id;
                usuario.rol = servidor.respuesta.rol;
                usuario.usuario = servidor.respuesta.usuario;
                usuario.nombre = servidor.respuesta.nombre;
                usuario.apellido1 = servidor.respuesta.apellido1;
                usuario.apellido2 = servidor.respuesta.apellido2;

                userInfo.text = "Conectado como: \n" + usuario.nombre + "\n" + usuario.apellido1 + "\n" + usuario.apellido2;


                break;
            case 402: // Faltan datos para ejecutar la acción solicitada
                print(servidor.respuesta.mensaje);
                break;

            case 404: // Error
                print("Error, no se puede conectar con el servidor");
                break;

            default:
                break;
        }
    }
}
