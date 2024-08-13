# Formula - Task Management System
![image](https://github.com/user-attachments/assets/371b4197-09b9-4166-a089-f7f67771742e)

## Описание

**Formula** - это простая система управления задачами, позволяющая пользователям создавать, редактировать и удалять задачи в разных категориях (столбцах). Проект разработан с использованием PHP и базы данных MySQL, что делает его легким и удобным для развертывания на любом стандартном веб-сервере.

## Требования

Перед установкой убедитесь, что ваша среда соответствует следующим требованиям:

- **PHP 7.4** или выше
- **MySQL 5.7** или выше
- **Apache/Nginx** (или любой другой веб-сервер)
- **Composer** (опционально, для установки зависимостей)
- Включенные модули PHP: `PDO`, `PDO_MySQL`, `Session`

## Установка

Для установки и запуска проекта выполните следующие шаги:

1. **Клонирование репозитория:**

   ```bash
   git clone https://github.com/eshkerata/formulafun/Formula.git
   cd Formula
   ```

2. **Настройка базы данных:**

   - Создайте базу данных MySQL:
     ```sql
     CREATE DATABASE formula_db;
     ```
   - Импортируйте структуру базы данных из файла `schema.sql`:
     ```bash
     mysql -u ваш_пользователь -p formula_db < schema.sql
     ```

3. **Настройка конфигурации:**

   - Скопируйте и настройте файл конфигурации:

     ```bash
     cp config.sample.php config.php
     ```

   - Откройте `config.php` и отредактируйте параметры подключения к базе данных:

     ```php
     $host = 'localhost';
     $db = 'formula_db';
     $user = 'ваш_пользователь';
     $pass = 'ваш_пароль';
     ```

4. **Развертывание на веб-сервере:**

   - Разместите файлы проекта в директории, доступной веб-серверу (например, `/var/www/html/`).
   - Убедитесь, что веб-сервер настроен на использование нужной директории.

5. **Настройка прав доступа:**

   - Убедитесь, что веб-сервер имеет права на запись в директорию сессий (обычно `/var/lib/php/sessions/`).

6. **Запуск проекта:**

   - Откройте браузер и перейдите по адресу вашего сервера, например `http://localhost/Formula/`.

## Команды

**Formula** поддерживает следующие основные команды и операции:

- **Создание нового столбца**: Заполните поле "Новый столбец" и нажмите кнопку "Создать новый столбец".
- **Добавление задачи в столбец**: Введите текст задачи в поле "Добавить задачу" под нужной колонкой и нажмите кнопку "плюс".
- **Редактирование названия столбца**: Введите новое название колонки и оно автоматически сохранится после потери фокуса поля ввода.
- **Редактирование задачи**: Введите новое название задачи и оно автоматически сохранится после потери фокуса поля ввода.
- **Удаление задачи**: Нажмите на иконку 🗑 рядом с задачей, после подтверждения задача будет удалена.
- **Удаление колонки**: Нажмите на кнопку "Удалить столбец" рядом с колонкой, после подтверждения колонка будет удалена со всеми задачами.

## Лицензия

Этот проект распространяется под лицензией **MIT License**. Вы можете свободно использовать, изменять и распространять код при условии сохранения оригинальной лицензии.

```
MIT License

Copyright (c) 2024 Эщкерята #innocamp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
