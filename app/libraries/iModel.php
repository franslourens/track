<?php

interface iModel
{
    public static function collection();
    public static function retrieveByPk($id);
    public function validate($data);
    public function save($data);
    public function delete();
    public function serialize();
}