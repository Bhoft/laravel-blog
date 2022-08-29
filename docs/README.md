* Task 1.1:
    * Implement a JSON REST API through which you can create, edit, list, view and delete news articles.

	* An article has a title, an author, a text (plaintext, html not needed), a creation date and a publication date.
	
	* The listing of articles should not contain the text (list endpoint)
	* The data should be stored in a MySQL database
	* No authentication with the api is needed
	

php artisan make:controller ArticleController 

php artisan make:model Models\Article -m


An article has a title, an author, a text (plaintext, html not needed), a creation date and a publication date.

also migration mit 
 	$table->text('title');
    $table->text('body');
    $table->text('author');
    $table->dateTime('publication_date')->nullable();
angelegt

ich bin am überlegen ob ich den user eventuell nutzen sollte??
