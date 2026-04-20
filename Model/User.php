<?php

// attr -> private + getters & setters + ctor + show()
class User
{
    // attributs
    private ?int $id;
    private string $nom;
    private string $email;
    private string $password;
    private float $poids;
    private float $taille;
    private string $objectif;

    // constructeur
    public function __construct($n, $e, $p, $poids, $taille, $obj)
    {
        $this->id = null;
        $this->nom = $n;
        $this->email = $e;
        $this->password = $p;
        $this->poids = $poids;
        $this->taille = $taille;
        $this->objectif = $obj;
    }

    // fonction show (comme la prof)
    function show()
    {
        echo "
        <table border='2'>
            <tr>
                <th>ID</th>
                <th>NOM</th>
                <th>EMAIL</th>
                <th>PASSWORD</th>
                <th>POIDS</th>
                <th>TAILLE</th>
                <th>OBJECTIF</th>
            </tr>
            <tr>
                <td>".$this->id."</td>
                <td>".$this->nom."</td>
                <td>".$this->email."</td>
                <td>".$this->password."</td>
                <td>".$this->poids."</td>
                <td>".$this->taille."</td>
                <td>".$this->objectif."</td>
            </tr>
        </table>
        ";
    }

    // getters & setters

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPoids()
    {
        return $this->poids;
    }

    public function setPoids($poids)
    {
        $this->poids = $poids;
        return $this;
    }

    public function getTaille()
    {
        return $this->taille;
    }

    public function setTaille($taille)
    {
        $this->taille = $taille;
        return $this;
    }

    public function getObjectif()
    {
        return $this->objectif;
    }

    public function setObjectif($objectif)
    {
        $this->objectif = $objectif;
        return $this;
    }
}

?>