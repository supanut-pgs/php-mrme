<?php
namespace MrMe\Web\v2;

abstract class AuthController extends Controller
{
	public abstract function login();
	public abstract function logout(); 
}

