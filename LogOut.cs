using System.Collections;
using System.Collections.Generic;
using UnityEngine.SceneManagement;
using UnityEngine;
using UnityEngine.Networking;
using UnityEngine.UI;


public class LogOut : MonoBehaviour
{
    public Button botonCerrarSesion;
    public GameObject canvasLoginManager;

    void Start()
    {
        // Asocia el mét0do CerrarSesionRequest al evento de clic del botón
        botonCerrarSesion.onClick.AddListener(CerrarSesionRequest);
    }

    void CerrarSesionRequest()
    {
        StartCoroutine(RealizarSolicitudCerrarSesion());
    }

    IEnumerator RealizarSolicitudCerrarSesion()
    {
        string url = "http://localhost/hospitalMedacB/cerrar_sesion.php";

        WWWForm form = new WWWForm();

        UnityWebRequest request = UnityWebRequest.Post(url, form);

        yield return request.SendWebRequest();

        if (request.result == UnityWebRequest.Result.Success)
        {
            Debug.Log("Sesión cerrada correctamente");
            canvasLoginManager = GameObject.Find("canvasLoginManager");
            canvasLoginManager.SetActive(false);
            SceneManager.LoadScene("loginScreen");
        }
        else
        {
            Debug.LogError("Error al cerrar la sesión: " + request.error);
        }
    }
}