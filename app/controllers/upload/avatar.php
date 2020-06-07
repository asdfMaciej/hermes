<?php
namespace Web\Controllers;
use \Model\User;

include_once ROOT_PATH . "/application/lib/bulletproof/bulletproof.php";
include_once ROOT_PATH . "/application/lib/bulletproof/utils/func.image-resize.php";

class Page extends \PageBuilder {
	protected function init() {
		$this->metadata->setTitle("Index");
		$this->addActions(["upload" => "onUpload"]);
	}

	protected function content() {}

	protected function onUpload() {
		$image = new \Bulletproof\Image($_FILES);
		if ($image["image"]) {
			$image->setMime(['jpeg', 'jpg', 'png']);
			$image->setLocation(ROOT_PATH . "/uploads/img/avatars");
			if ($success = $image->upload()) {
				\Bulletproof\Utils\resize(
					$image->getFullPath(),
					$image->getMime(),
					$image->getWidth(),
					$image->getHeight(),
					512, 512, // width x height
					true // keep the ratio
				);

				$user = User::getSingleItem($this->database, ["user_id" => $this->account->user_id]);
				$user->avatar = "uploads\\img\\avatars\\" . $image->getName().".".$image->getMime();
				$success = $user->save($this->database);
			}
		}
		return $this->redirect("profile/".$this->account->user_id);
	}
}

new Page();
?>