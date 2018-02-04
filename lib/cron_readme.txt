Programar una tarea cron en el servidor
***************************************

Para el correcto funcionamiento del CMS se debe programar una tarea 
que se ejecute de forma autónoma al menos cada 15 minutos.

Para un mejor rendimiento se aconseja que la tarea se ejecute cada 
4 ó 5 minutos.

***************************************
Programar una tarea en Plesk
----------------------------
Acceder al dominio, luego a "Tareas programadas" y
crear una tarea que ejecute un comando:
curl http://midominio.com/lib/cron.php

Ejecutar la tarea, al estilo cron y escribir: 
*/4 * * * *

(Esto ejecutará la tarea cada 4 minutos)

***************************************
Programar una tarea mediante consola
------------------------------------

Acceder a la consola del servidor mediante SSH con una cuenta
con los permisos oportunos y teclear:

crontab */4 * * * * curl http://midominio.com/lib/cron.php

(Esto ejecutará la tarea cada 4 minutos)