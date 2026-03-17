<p align="center">
  <img src="img/absentia.png" alt="GesTurmas Logo" width="300">
</p>

# Absentia
Moodle block to identify dropout risk students and provide additional  contact info, via web service connected to the academic management system

A Moodle block designed to help teachers and course managers identify students at risk of dropping out. It lists students who have not accessed a course for a specified period and provides their contact information, including an alternative email address fetched from an external academic management system via a web service.

This block was originally developed for [Universidade Aberta (UAb)](https://portal.uab.pt/).

## Features

*   **Identifies Inactive Students**: Lists students enrolled in a course who have not logged in for a configurable period.
*   **Time Filters**: Filter students by inactivity period: more than 2 weeks, more than 30 days, or never accessed.
*   **External Data Integration**: Connects to an external web service to retrieve students' personal email addresses, providing an alternative contact method.
*   **Easy Communication**:
    *   Click on a student's name to open your default email client with a pre-filled `mailto:` link.
    *   The email subject and body can be customized for each block instance.
    *   A "contact all" button generates a `mailto:` link to send a bulk email to all identified students (using BCC).
*   **Data Export**: Export the list of at-risk students and their details (name, student number, last access, personal email, UAb email) to a CSV file.
*   **Configurable Display Rules**: The block only displays during the official academic calendar periods (semesters), which are configured globally by the site administrator.
*   **Permissions**: The block is only visible to users with the capability to update courses (e.g., teachers, managers).

## Requirements

*   Moodle 3.x or higher.
*   A web service that accepts a list of student usernames and returns their personal contact information in XML format.
*   The block is designed to work with specific course `idnumber` formats, but the logic in `block_absentia.php` can be adapted for other institutional needs.

## Installation

1.  Copy the `absentia` directory into the `blocks` directory of your Moodle installation.
    ```
    {your_moodle_root}/blocks/absentia
    ```
2.  Log in to your Moodle site as an administrator.
3.  Navigate to **Site administration > Notifications**. Moodle will detect the new plugin and guide you through the installation process (including database table creation).

## Configuration

The block has two levels of configuration: site-wide (admin) and per-instance (teacher).

### Site Administration Settings

A site administrator can configure the global settings under **Site administration > Plugins > Blocks > Absentia**.

*   **Academic Calendar (1st and 2nd Semester)**:
    *   `Início` (Start Date): The start date of the semester (format: `YYYY-MM-DD`).
    *   `Fim` (End Date): The end date of the semester (format: `YYYY-MM-DD`).
*   **Validation Start Delay**:
    *   `Número de dias` (Number of days): A delay (in days) after the semester starts before the block begins checking for inactivity. This prevents flagging students at the very beginning of the term.
*   **Intermediate Database (BDInt) Web Service**:
    *   `Host`: The database server host.
    *   `Porta` (Port): The database server port.
    *   `Designação` (Name): The name of the intermediate database.
    *   `Utilizador` (User): The username for the database connection.
    *   `Palavra-passe` (Password): The password for the database connection.
    *   `Web Service`: The URL of the web service that connects to the intermediate database to fetch student data.

### Block Instance Settings

When a teacher adds the "Absentia" block to a course page, they can configure settings specific to that instance.

*   **Calendário Lectivo (Academic Calendar)**:
    *   `Semestre`: Specify the semester for the course (`1` for 1st semester, `2` for 2nd semester, `A` for Annual). This determines which academic calendar dates are used.
*   **E-mail a enviar (Email to send)**:
    *   `Assunto` (Subject): The default subject line for the `mailto:` links.
    *   `Corpo` (Body): The default body text for the `mailto:` links. You can use this to create a template message.

## Usage

1.  As a teacher or course manager, navigate to your course page.
2.  Turn editing on.
3.  Add the **Absentia** block from the "Add a block" menu.
4.  Configure the block instance settings (Semester, default email Subject/Body).
5.  The block will automatically display a list of students who meet the inactivity criteria based on the selected filter (+2 weeks, +30 days, or never).
6.  Click on a student's name to email them individually.
7.  Click the "contact all" icon to email the entire list.
8.  Click the "export to CSV" icon to download the data for offline use.

The block will only show content if the current date is within the configured academic semester and after any specified start delay.

## Licenses

**Author**: Bruno Tavares  
**Contact**: [brunustavares@gmail.com](mailto:brunustavares@gmail.com)  
**LinkedIn**: [https://www.linkedin.com/in/brunomastavares/](https://www.linkedin.com/in/brunomastavares/)  
**Copyright**: 2019-present Bruno Tavares  
**License**: GNU GPL v3 or later  

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

### Assets

- **Source code**: GNU GPL v3 or later (© Bruno Tavares)  
- **Images**: © Universidade Aberta, provided by the Digital Production Services, all rights reserved. Usage subject to the institution's policy.
