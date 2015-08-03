#!/bin/bash
for ((i=o;i<100;i++));do
{
    sleep 3;
    php payment.php 12306 >> payment.log
}&
done
wait
