<?php

abstract class Config{
    const SITENAME = "MvcProject";
    const SECRET = "5MVC5";
    const ADDRESS = "http://mvcproject.com";
    const ADMNAME = "Steve";
    const ADM_EMAIL = "stopa6767@gmail.com";
    const LOCAL = "uk_UA";

    const DB_HOST = "localhost";
    const DB_USER = "Steve";
    const DB_PASSWORD = "nokia675320";
    const DB_NAME = "mvc";
    const DB_PREFIX = "xyz_";

    const DIR_IMG = "/images/";
    const DIR_IMG_ARTICLES = "/images/articles/";
    const DIR_IMG_AVATAR = "/images/avatar/";
    const DIR_TMPL = "/domains/mvc/tmpl/";
    const DIR_EMAILS = "/domains/mvc/tmpl/emails/";

    const FILE_MESSAGES = "/domains/mvc/text/messages.ini";

    const COUNT_ARTICLES_ON_PAGE = 3;
    const COUNT_SHOW_PAGES = 10;

    const MIN_SEARCH_LEN = 3;
    const LEN_SEARCH_RES = 255;

    const DEFAULT_AVATAR = "default.png";
    const MAX_SIZE_AVATAR = 51200;
}

?>