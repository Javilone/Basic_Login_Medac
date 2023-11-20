using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class EnlacesWeb : MonoBehaviour
{
    public void EnlacesDeBoton(string enlace)
    {
        Application.OpenURL(enlace);
    }
}
