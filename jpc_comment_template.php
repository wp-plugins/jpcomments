<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ): ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	
	endif; ?>

<!-- You can start editing here. -->

<?php 
if ( have_comments() ) : ?>

	<ol class="commentlist">
		<?php 
		wp_list_comments(array('reply_text'=>'Reply to this Comment','type'=>'comment','callback' => 'jpc_comments'));  ?>
	</ol>

<?php 
else : // this is displayed if there are no comments so far ?>

	<?php 
	if ( comments_open() ) : ?>
	
		<!-- If comments are open, but there are no comments. -->

	 <?php 
	 else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Comments are closed.</p>

	<?php 
	endif; ?>

<?php 
endif; ?>


<?php 

if ( comments_open() ) : ?>

<div id="respond">

	<div class="cancel-comment-reply">
		<small><?php cancel_comment_reply_link(); ?></small>
	</div>
	
	<?php 
	if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
	
		<p>You must be <a href="<?php echo wp_login_url( get_permalink() ); ?>">logged in</a> to post a comment.</p>
	
	<?php 
	else : ?>
	
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		
			<?php
			$author_value = isset($_COOKIE['comment_author_'.COOKIEHASH]) ? $_COOKIE['comment_author_'.COOKIEHASH] : "Twitter Username"; 
			$no_remove = isset($_COOKIE['comment_author_'.COOKIEHASH]) ? ' class="no_remove"' : null; ?>
		
			<p class="twitter_comment"><input type="text" name="author" id="author" value="<?php echo $author_value; ?>" size="22" tabindex="1"<?php echo $no_remove; ?>/></p>
			
			<input type="hidden" name="email" id="email" value="blank@blank.com" />
			<input type="hidden" name="url" id="url" />
		
			<p><textarea name="comment" id="comment" cols="58" rows="10" tabindex="4">Write a comment...</textarea></p>
			
			<p><input name="submit" type="submit" id="submit" tabindex="5" value="Comment" />
	
			<?php comment_id_fields(); ?>
	
			</p>
	
			<?php do_action('comment_form', $post->ID); ?>
		
		</form>
	
	<?php 
	endif; // If registration required and not logged in ?>

</div> <!-- respond -->

<?php 
endif; // if you delete this the sky will fall on your head ?>
