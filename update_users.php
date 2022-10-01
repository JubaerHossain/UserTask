<?

function validateUsers($data)
{
    $errors = array();
    if (empty($data['name'])) {
        $errors['name'] = 'Name is required';
    }
    if (empty($data['email'])) {
        $errors['email'] = 'Email is required';
    }
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    }
    return $errors;
}

function updateUsers($users)
{
    try {
        $users = validateUsers($users);
        if (empty($users['errors'])) {
            foreach ($users as $user) {
                DB::table('users')->where('id', $user['id'])->update([
                    'name' => $user['name'],
                    'login' => $user['login'],
                    'email' => $user['email'],
                    'password' => md5($user['password'])
                ]);
            }
            return Redirect::back()->with(['success', 'All users updated.']);
        } else {
            return Redirect::back()->withErrors(['error', ['We couldn\'t update user: ' . $users['errors']]]);
        }
    } catch (\Throwable $th) {
        return Redirect::back()->withErrors(['error', ['We couldn\'t update user: ' . $th->getMessage()]]);
    }
}
function sendEmail($users)
{
    try {
        foreach ($users as $user) {
            $message = 'Account has beed created. You can log in as <b>' . $user['login'] . '</b>';
            if ($user['email']) {
                Mail::to($user['email'])
                    ->cc('support@company.com')
                    ->subject('New account created')
                    ->queue($message);
            } else {
                return Redirect::back()->withErrors(['error', ['We couldn\'t send email: ' . $user['email']]]);
            }
        }
    } catch (\Throwable $th) {
        return Redirect::back()->withErrors(['error', ['We couldn\'t send email: ' . $th->getMessage()]]);
    }
}

function storeUsers($users)
{
    try {
        $users = validateUsers($users);
        if (empty($users['errors'])) {
            foreach ($users as $user) {
                DB::table('users')->insert([
                    'name' => $user['name'],
                    'login' => $user['login'],
                    'email' => $user['email'],
                    'password' => md5($user['password'])
                ]);
            }
            $this->sendEmail($users);
            
            return Redirect::back()->with(['success', 'All users created.']);
        } else {
            return Redirect::back()->withErrors(['error', ['We couldn\'t store user: ' . $users['errors']]]);
        }
    } catch (\Throwable $th) {
        return Redirect::back()->withErrors(['error', ['We couldn\'t store user: ' . $th->getMessage()]]);
    }
}
