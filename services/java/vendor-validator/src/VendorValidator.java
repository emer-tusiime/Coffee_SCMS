import java.util.regex.Pattern;

public class VendorValidator {

    // Example: vendor name must be 3-50 chars, letters, spaces, and hyphens only
    private static final Pattern VENDOR_NAME_PATTERN = Pattern.compile("^[A-Za-z\\s-]{3,50}$");

    public static boolean validateVendorName(String name) {
        if (name == null) return false;
        return VENDOR_NAME_PATTERN.matcher(name).matches();
    }

    // You can add more validation logic here, e.g., checking against a blacklist
}