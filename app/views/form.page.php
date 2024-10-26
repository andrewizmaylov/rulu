<?php

require_once('app/views/partials/sidebar.php');

if (!$response['success']) { ?>
    <section class="grid place-content-center text-[80px] font-bold opacity-20">
        User not found
    </section>
<?php
} else {
	$user = $response['result'];
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		$action = $_POST['action'];

		try {
			// Perform action based on which button was clicked
			switch ($action) {
				case 'create':
					$response = json_decode(UserService::createUser(), true);
					$data = $response['result'];
					break;

				case 'update':
					$response = json_decode(UserService::updateUser(), true);
					$data = $response['result'];
					break;

				case 'delete':
					$response = json_decode(UserService::deleteUser(), true);
					break;

				default:
					echo "Invalid action.";
					break;
			}
		} catch (Exception $e) {
			echo "An error occurred: ".$e->getMessage();
		}
	}
	?>


    <form action="" method="POST">
        <div class="sm:col-span-3">
            <label for="full_name"
                   class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
            <div class="mt-2">
                <input type="text" name="full_name" id="full_name" autocomplete="full_name"
                       value="<?= $user['full_name'] ?? null; ?>"
                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
        </div>
        <div class="sm:col-span-3">
            <label for="role"
                   class="block text-sm font-medium leading-6 text-gray-900">Role</label>
            <div class="mt-2">
                <input type="text" name="role" id="role" autocomplete="role"
                       value="<?= $user['role'] ?? null; ?>"
                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
        </div>
        <div class="sm:col-span-3">
            <label for="efficiency"
                   class="block text-sm font-medium leading-6 text-gray-900">Efficiency</label>
            <div class="mt-2">
                <input type="number" name="efficiency" id="efficiency" autocomplete="efficiency" min="0" max="100"
                       value="<?= $user['efficiency'] ?? null; ?>"
                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
        </div>
        <section class="mt-6 flex item-center justify-between">
            <button type="submit"
                    name="action"
                    value="<?= isset($user) ? 'update' : 'create' ?>"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Save user
            </button>
            <button type="submit"
                    name="action"
                    value="delete"
                    class="block rounded-md bg-red-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                Delete user
            </button>
        </section>
    </form>
<?php
}
require_once('app/views/partials/footer.php');
?>


