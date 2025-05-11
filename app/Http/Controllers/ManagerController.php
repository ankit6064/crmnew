<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ManagerController extends Controller
{
    /**
     * Navigate to the source listing view.
     * @return \Illuminate\View\View
     *
     */
    public function index()
    {
        return view('manager.index');
    }

    /**
     * Handle the AJAX request for DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getManagers(Request $request)
    {
        if ($request->ajax()) {
            $managers = User::where('is_admin', MANAGER)
                ->select('id', 'first_name', 'last_name', 'image', 'email', 'orignal_password', 'address', 'phone_no', 'manager_type','is_active')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($managers)
                ->editColumn('manager_type', function ($data) {
                    $mangerType = $data->manager_type == MANAGER_TYPE_INTERNAL
                        ? 'Internal'
                        : ($data->manager_type == MANAGER_TYPE_EXTERNAL
                            ? 'External'
                            : 'Not assigned');

                    return $mangerType;
                })
                ->addColumn('status', function ($data) {
                    // Determine if the checkbox should be checked
                    $checked = $data->is_active == 1 ? 'checked' : '';
                    $status = '<input data-sid = "' . $data->source_id . '" class="switchery" type="checkbox" ' . $checked . '>';
                    return $status;
                })

                ->addColumn('actions', function ($data) {
                    // Customize the action buttons
                    $viewLink = '<a href="' . route('manager.employees', ['manager_id' => $data->id]) . '">
                 <span class="material-symbols-outlined text-secondary viewEmployee">groups</span>
              </a>';


                    $editLink = '<a href="' . route('manager.edit', ['manager_id' => $data->id]) . '">
                    <span class="material-symbols-outlined text-success editManager">edit_square</span>
                </a>';

                    $deleteLink = '<a href="javascript:void(0);" class="delete-manager" data-id="' . $data->id . '">
                <span class="material-symbols-outlined text-danger deleteManager">delete</span>
            </a>';


                    return $viewLink . ' ' . $editLink . '' . $deleteLink;
                })
                ->rawColumns(['actions', 'status'])
                ->toJson();
        }
        return response()->json(['error' => 'Invalid request'], 400);

    }

    /**
     * Display the manager's employees page.
     *
     * This function retrieves the full name of a manager by concatenating their first and last names
     * and passes this data to the view to be displayed.
     * The function also ensures that if the manager ID is not valid or not found, an appropriate error is handled.
     *
     * @param int $managerID The ID of the manager whose employees need to be displayed.
     * @return \Illuminate\View\View The view showing the manager's full name along with any relevant employee data.
     */
    public function managerEmloyees($managerID)
    {
        try {
            // Retrieve the manager's full name by concatenating their first and last names
            $managerName = User::where('id', $managerID)
                ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name") // Concatenate first name and last name
                ->first(); // Get the first record (since manager ID is unique)

            // Pass the manager's full name to the view to display it
            return view('manager.employees', compact('managerName','managerID')); // compact('managerName') passes the variable to the view
        } catch (\Exception $e) {
            // Log the exception message for debugging purposes
            \Log::error('Error retrieving manager data: ' . $e->getMessage());

            // Redirect back to the previous page with an error message
            return redirect()->route('manager.index')->with('swalError', $e->getMessage());
        }
    }


    public function getManagerEmloyees(Request $request, $managerID)
    {

        if ($request->ajax()) {

            $mangaerEmployees = User::where(['user_id' => $managerID, 'is_admin' => USER])
                ->select('id', 'first_name', 'last_name', 'image', 'email', 'orignal_password', 'address', 'phone_no', 'manager_type','is_active')
                ->get();

            return DataTables::of($mangaerEmployees)
                ->editColumn('manager_type', function ($data) {
                    $mangerType = $data->manager_type == MANAGER_TYPE_INTERNAL
                        ? 'Internal'
                        : ($data->manager_type == MANAGER_TYPE_EXTERNAL
                            ? 'External'
                            : 'Not assigned');

                    return $mangerType;
                })
                ->addColumn('status', function ($data) {
                    // Determine if the checkbox should be checked
                    $checked = $data->is_active == 1 ? 'checked' : '';
                    $status = '<input data-sid = "' . $data->source_id . '" class="switchery" type="checkbox" ' . $checked . '>';
                    return $status;
                })
                ->addColumn('actions', function ($data) {
                    // Customize the action buttons
                    $assigned = "";
                    $viewLink = '<a href="' . route('manager.employees', ['manager_id' => $data->id]) . '">
                 <span class="material-symbols-outlined text-secondary viewEmployee">visibility</span>';

                    $editLink = '<a href="' . route('manager.edit', ['manager_id' => $data->id]) . '">
                    <span class="material-symbols-outlined text-success editEmployee">edit_square</span>
                </a>';

                $deleteLink = '<a href="javascript:void(0);" class="delete-manager" data-id="' . $data->id . '" onclick="deleteemployee('.$data->id.')">
                <span class="material-symbols-outlined text-danger deleteManager">delete</span>
            </a>';

                    return $assigned  . ' ' . $editLink . '' . $deleteLink;
                })
                ->rawColumns(['actions'])
                ->toJson();
        }
        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Show the form to create a new manager.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $request = 'StoreManagerRequest';
        // Return the view with the manager creation form
        return view('manager.create', compact('request'));
    }


    // Handle the form submission to save a manager
    public function store(StoreManagerRequest $request)
    {
        try {
            // The request is already validated, no need to manually validate here
            // Generate a random password
            $password = Str::random(12);

            // Add a new value to the request data
            $request->merge([
                'user_id' => auth()->user()->id,
                'name' => $request->first_name . ' ' . $request->last_name,
                'orignal_password' => $password,
                'password' => Hash::make($password),
                'is_admin' => MANAGER,
            ]);

            // Create the new user (manager)
            User::create($request->all());
            // Redirect with a success message
            return redirect()->route('manager.index')->with('success', 'Manager created successfully!');
        } catch (\Exception $e) {
            // Catch any other general exception and log it
            \Log::error('Error creating manager: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);
            // Redirect back with a general error message
            return redirect()->back()->with('error', 'An error occurred while creating the manager. Please try again.');
        }
    }

    // Display the form for editing the manager
    public function edit($managerID)
    {

        try {
            // Try to find the manager by ID
            $manager = User::findOrFail($managerID);

            $request = 'UpdateManagerRequest';

            // Return the edit view with the manager data
            return view('manager.edit', compact('manager', 'request'));
        } catch (ModelNotFoundException $e) {
            // Handle the case where the manager is not found
            return redirect()->route('manager.index') // Redirect to the manager list page or any other route
                ->with('error', 'Manager not found!');
        } catch (\Exception $e) {
            // Handle any other exceptions that may occur
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }


    public function update(UpdateManagerRequest $request, $id)
    {
        try {
            // The request is already validated here, so you can proceed with the update logic
            $manager = User::findOrFail($id);
            $validatedData = $request->validated();
            // Add a new value to the request data
            $request->merge([
                'name' => $request->first_name . ' ' . $request->last_name,
                'password' => Hash::make($request->orignal_password)
            ]);
            // Update the manager with the validated data
            $manager->update($request->all());

            // Redirect with a success message
            return redirect()->route('manager.index')->with('success', 'Manager updated successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the manager is not found
            return redirect()->route('manager.index') // Redirect to the manager list page or any other route
                ->with('error', 'Manager not found!');
        } catch (\Exception $e) {
            // Handle any other exceptions that may occur
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function destroy($manager_id)
    {
        try {
            // Find the manager by ID
            $manager = User::findOrFail($manager_id); // Will throw ModelNotFoundException if not found

            // Delete the manager
            $manager->delete();

            // Redirect with a success message
            return redirect()->route('manager.index')->with('success', 'Manager deleted successfully.');

        } catch (ModelNotFoundException $e) {
            // Handle the case when the manager is not found
            return redirect()->route('manager.index')->with('error', 'Manager not found.');

        } catch (\Exception $e) {
            // Handle general exceptions (e.g., database errors)
            return redirect()->route('manager.index')->with('error', 'An error occurred while trying to delete the manager.');
        }
    }
}
